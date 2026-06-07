<?php

namespace App\Services;

use App\Models\Race;
use App\Models\RaceWeather;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    private string $coreApiUrl;

    public function __construct()
    {
        $this->coreApiUrl = rtrim(config('services.core_api.url', 'http://localhost:8100'), '/');
    }

    // 실패 후 재시도 유예 시간 (시간 단위)
    private const RETRY_AFTER_HOURS = 24;

    /**
     * 대회 날씨 조회.
     *
     * 호출 순서:
     *   1. race_weather 테이블에 데이터 있으면 즉시 반환 (중복 호출 없음)
     *   2. 미래 대회 → null
     *   3. 이미 시도했고 유예 시간(24h) 미경과 → null (반복 호출 방지)
     *   4. CORE API 호출 1회 — 성공/실패 무관하게 시도 시각 기록
     */
    public function getForRace(Race $race): ?RaceWeather
    {
        // 1. DB에 날씨 데이터 있음 → 즉시 반환
        $cached = RaceWeather::where('race_id', $race->id)->first();
        if ($cached) {
            return $cached;
        }

        // 2. 미래 대회 → 날씨 없음
        if ($race->race_date->isFuture()) {
            return null;
        }

        // 3. 이미 시도한 적 있고 유예 시간 미경과 → 재호출 방지
        if ($race->weather_fetch_attempted_at &&
            $race->weather_fetch_attempted_at->gt(now()->subHours(self::RETRY_AFTER_HOURS))) {
            return null;
        }

        // 4. CORE API 호출 — 시도 시각을 먼저 기록해 동시 요청 중복 방지
        $race->updateQuietly(['weather_fetch_attempted_at' => now()]);

        try {
            $response = Http::timeout(10)
                ->post("{$this->coreApiUrl}/api/weather/race/{$race->id}");

            if ($response->successful()) {
                return RaceWeather::where('race_id', $race->id)->first();
            }

            Log::warning("날씨 수집 실패 race_id={$race->id}: " . $response->status());
        } catch (\Exception $e) {
            Log::warning("날씨 API 연결 오류 race_id={$race->id}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * 장소명 → 기상청 지점코드 자동 추론 (CORE API 위임).
     * 대회 등록/수정 시 호출하여 races.weather_stn_id 자동 설정.
     */
    public function autoResolveStn(Race $race): void
    {
        // 이미 지점코드가 수동 지정되어 있으면 덮어쓰지 않음
        if ($race->weather_stn_id) {
            return;
        }

        try {
            $response = Http::timeout(8)->post("{$this->coreApiUrl}/api/weather/resolve-stn", [
                'location' => $race->location ?? '',
                'city'     => $race->city ?? '',
            ]);

            if ($response->successful()) {
                $stnId = $response->json('stn_id');
                if ($stnId) {
                    $race->update(['weather_stn_id' => $stnId]);
                }
            }
        } catch (\Exception $e) {
            Log::info("지점코드 자동추론 실패 race_id={$race->id}: " . $e->getMessage());
        }
    }

    /**
     * 날씨 상태 아이콘 반환.
     */
    public static function conditionIcon(?string $condition): string
    {
        return match($condition) {
            '맑음'      => '☀️',
            '구름조금'  => '🌤',
            '구름많음'  => '⛅',
            '흐림'      => '☁️',
            '비'        => '🌧',
            '눈'        => '❄️',
            '진눈깨비'  => '🌨',
            '소나기'    => '⛈',
            '안개'      => '🌫',
            default     => '🌡',
        };
    }
}

<?php

namespace App\Services;

use App\Models\Race;
use App\Models\RaceEdition;
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

    /**
     * 대회 날씨 조회.
     *
     * 방어 전략: race_weather 테이블이 단일 캐시.
     *   - DB에 레코드 있음 → 즉시 반환 (API 재호출 없음)
     *   - DB에 없음 → CORE API 1회 호출
     *     - 성공: 날씨 데이터 저장 → 반환
     *     - 실패/데이터 없음: 빈 마커 레코드 저장 → 이후 재호출 차단
     */
    public function getForRace(Race $race): ?RaceWeather
    {
        // 1. DB에 레코드 있음 → 즉시 반환 (성공 데이터 또는 빈 마커 모두 포함)
        $cached = RaceWeather::where('race_id', $race->id)->first();
        if ($cached) {
            return $cached->temperature !== null ? $cached : null;
        }

        // 2. 미래 대회 → 날씨 없음 (최신 edition 기준)
        $latestDate = $race->editions()->orderByDesc('year')->value('race_date');
        if (!$latestDate || \Carbon\Carbon::parse($latestDate)->isFuture()) {
            return null;
        }

        // 3. CORE API 1회 호출
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

        // 4. 실패 시 빈 마커 레코드 저장 → 이후 재호출 차단
        RaceWeather::upsert(
            [['race_id' => $race->id, 'fetched_at' => now()]],
            ['race_id'],
            ['fetched_at']
        );

        return null;
    }

    /**
     * race_editions 기반 지점코드 자동 추론.
     * 대회 인스턴스 등록/수정 시 호출.
     */
    public function autoResolveStnForEdition(RaceEdition $edition): void
    {
        if ($edition->weather_stn_id) {
            return;
        }

        try {
            $response = Http::timeout(8)->post("{$this->coreApiUrl}/api/weather/resolve-stn", [
                'location' => $edition->location ?? '',
                'city'     => $edition->city ?? '',
            ]);

            if ($response->successful()) {
                $stnId = $response->json('stn_id');
                if ($stnId) {
                    $edition->update(['weather_stn_id' => $stnId]);
                }
            }
        } catch (\Exception $e) {
            Log::info("지점코드 자동추론 실패 edition_id={$edition->id}: " . $e->getMessage());
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

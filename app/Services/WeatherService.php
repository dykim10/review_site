<?php

namespace App\Services;

use App\Models\Race;
use App\Models\RaceEdition;
use App\Models\RaceWeather;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    private string $coreApiUrl;

    public function __construct()
    {
        $this->coreApiUrl = rtrim(config('services.core_api.url', 'http://localhost:8100'), '/');
    }

    public function getForRace(Race $race): ?RaceWeather
    {
        $edition = $race->latestEdition;
        if (! $edition) {
            return null;
        }

        return $this->getForEdition($edition);
    }

    public function selectHistoryEditions(Collection $editions, int $years = 5): Collection
    {
        return $editions
            ->filter(fn (RaceEdition $edition) => $edition->race_date && ! $edition->race_date->isFuture())
            ->sortByDesc('year')
            ->take($years)
            ->values();
    }

    public function getHistoryForEditions(Collection $editions, int $years = 5): Collection
    {
        return $this->selectHistoryEditions($editions, $years)
            ->map(fn (RaceEdition $edition) => [
                'edition' => $edition,
                'weather' => $this->getForEdition($edition),
            ])
            ->values();
    }

    public function getForEdition(RaceEdition $edition): ?RaceWeather
    {
        $cached = $this->cachedWeather($edition);
        if ($cached !== false) {
            return $cached;
        }

        if (! $edition->race_date || \Carbon\Carbon::parse($edition->race_date)->isFuture()) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->post("{$this->coreApiUrl}/api/weather/race/{$edition->id}");

            if ($response->successful()) {
                return RaceWeather::where('race_edition_id', $edition->id)->first();
            }

            Log::warning("날씨 수집 실패 edition_id={$edition->id}: " . $response->status());
        } catch (\Exception $e) {
            Log::warning("날씨 API 연결 오류 edition_id={$edition->id}: " . $e->getMessage());
        }

        RaceWeather::updateOrCreate(
            ['race_edition_id' => $edition->id],
            ['fetched_at' => now()]
        );

        return null;
    }

    /** @return RaceWeather|null|false  false = 캐시 행 없음 */
    private function cachedWeather(RaceEdition $edition): RaceWeather|null|false
    {
        $cached = $edition->relationLoaded('weather')
            ? $edition->weather
            : RaceWeather::where('race_edition_id', $edition->id)->first();

        if (! $cached) {
            return false;
        }

        return $cached->temperature !== null ? $cached : null;
    }

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

    public static function conditionIcon(?string $condition): string
    {
        return match ($condition) {
            '맑음'     => '☀️',
            '구름조금' => '🌤',
            '구름많음' => '⛅',
            '흐림'     => '☁️',
            '비'       => '🌧',
            '눈'       => '❄️',
            '진눈깨비' => '🌨',
            '소나기'   => '⛈',
            '안개'     => '🌫',
            default    => '🌡',
        };
    }
}

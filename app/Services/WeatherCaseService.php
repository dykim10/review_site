<?php

namespace App\Services;

use App\Models\RaceWeatherCase;
use App\Models\Review;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherCaseService
{
    public function upsertFromReview(Review $review): void
    {
        $review->loadMissing('raceEdition.weather');
        $edition = $review->raceEdition;
        $weather = $edition?->weather;

        if (! $edition || ! $weather || ! $review->finish_time) {
            return;
        }

        $seconds = $this->parseFinishSeconds($review->finish_time);
        if ($seconds === null) {
            return;
        }

        $parsed = $review->parsed_watch_data ?? [];

        RaceWeatherCase::updateOrCreate(
            ['review_id' => $review->id],
            [
                'race_edition_id'     => $edition->id,
                'temperature'         => $weather->temperature,
                'humidity'            => $weather->humidity !== null ? (int) $weather->humidity : null,
                'wind_speed'          => $weather->wind_speed,
                'finish_time_seconds' => $seconds,
                'pace_splits'         => $parsed['pace_splits'] ?? null,
                'heart_rate_splits'   => $parsed['hr_splits'] ?? $parsed['heart_rate_splits'] ?? null,
                'is_certified'        => (bool) $review->is_certified,
                'source'              => $review->source ?? 'manual',
            ]
        );

        $this->requestEmbeddingIfNeeded($review);
    }

    private function parseFinishSeconds(string $finishTime): ?int
    {
        $parts = explode(':', trim($finishTime));
        if (count($parts) === 3) {
            return (int) $parts[0] * 3600 + (int) $parts[1] * 60 + (int) $parts[2];
        }
        if (count($parts) === 2) {
            return (int) $parts[0] * 60 + (int) $parts[1];
        }

        return null;
    }

    private function requestEmbeddingIfNeeded(Review $review): void
    {
        $case = RaceWeatherCase::where('review_id', $review->id)->first();
        if (! $case) {
            return;
        }

        $review->loadMissing('raceEdition.race');
        $race = $review->raceEdition?->race;

        try {
            Http::timeout(30)->post(config('services.core_api.url') . '/api/rag/embed', [
                'case_id'             => $case->id,
                'race_name'           => $race?->name ?? '마라톤',
                'year'                => $review->raceEdition?->year,
                'course_type'         => $review->course_type,
                'temperature'         => $case->temperature,
                'humidity'            => $case->humidity,
                'wind_speed'          => $case->wind_speed,
                'finish_time_seconds' => $case->finish_time_seconds,
                'live'                => app()->environment('production'),
            ]);
        } catch (\Throwable $e) {
            Log::warning('[WeatherCase] embedding 요청 실패: ' . $e->getMessage());
        }
    }
}

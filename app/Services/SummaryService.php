<?php

namespace App\Services;

use App\Models\Race;
use App\Models\Review;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SummaryService
{
    public function summarize(Review $review, Race $race): void
    {
        try {
            $response = Http::timeout(15)->post(
                config('services.core_api.url') . '/api/summarize',
                [
                    'content'   => $review->content,
                    'race_name' => $race->name,
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $review->update([
                    'ai_summary' => $data['summary'] ?? null,
                    'sentiment'  => $data['sentiment'] ?? null,
                ]);
            } else {
                Log::warning('AI 개별 요약 응답 오류', ['status' => $response->status()]);
            }
        } catch (\Exception $e) {
            Log::warning('AI 개별 요약 실패: ' . $e->getMessage());
        }
    }

    public function markRaceSummaryDirty(Race $race): void
    {
        $summary = $race->ai_race_summary ?? [];
        if (! is_array($summary)) {
            $summary = [];
        }
        $summary['_meta'] = array_merge($summary['_meta'] ?? [], [
            'dirty'    => true,
            'dirty_at' => now()->toIso8601String(),
        ]);
        $race->update(['ai_race_summary' => $summary]);
    }

    public function summarizeRace(Race $race): void
    {
        $editionIds = $race->editions()->pluck('id');

        $reviews = Review::whereIn('race_edition_id', $editionIds)
            ->latest()
            ->limit(50)
            ->pluck('content')
            ->toArray();

        if (empty($reviews)) {
            return;
        }

        $avgRating = Review::whereIn('race_edition_id', $editionIds)->avg('rating') ?? 0;

        try {
            $response = Http::timeout(30)->post(
                config('services.core_api.url') . '/api/races/summarize',
                [
                    'race_name'   => $race->name,
                    'reviews'     => $reviews,
                    'avg_rating'  => round($avgRating, 1),
                    'total_count' => count($reviews),
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                if (! is_array($data)) {
                    $data = [];
                }
                $data['_meta'] = array_merge($data['_meta'] ?? [], [
                    'dirty'        => false,
                    'generated_at' => now()->toIso8601String(),
                ]);
                $race->update(['ai_race_summary' => $data]);
            } else {
                Log::warning('AI 대회 종합 요약 응답 오류', ['status' => $response->status()]);
            }
        } catch (\Exception $e) {
            Log::warning('AI 대회 종합 요약 실패: ' . $e->getMessage());
        }
    }
}

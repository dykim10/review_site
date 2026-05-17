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
                Log::warning('AI 요약 응답 오류', ['status' => $response->status()]);
            }
        } catch (\Exception $e) {
            // CORE API 장애 시 리뷰 등록 자체는 정상 처리
            Log::warning('AI 요약 실패: ' . $e->getMessage());
        }
    }
}

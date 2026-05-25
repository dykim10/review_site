<?php

namespace App\Services;

use App\Models\Hashtag;
use App\Models\Review;

class HashtagService
{
    /**
     * 입력 문자열 → 정규화된 해시태그 이름 배열
     * 입력 예: "#마라톤 서울대회, 풀코스" → ["마라톤", "서울대회", "풀코스"]
     */
    public function parseInput(?string $raw): array
    {
        if (!$raw || trim($raw) === '') return [];

        $tokens = preg_split('/[\s,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        $names  = [];

        foreach ($tokens as $token) {
            $name = trim(ltrim(trim($token), '#'));
            $name = preg_replace('/\s+/', '', $name); // 내부 공백 제거

            if ($name === '' || mb_strlen($name) > 30) continue;

            $names[] = $name;
        }

        // 최대 10개, 중복 제거
        return array_values(array_unique(array_slice($names, 0, 10)));
    }

    /**
     * 해시태그 upsert: 기존 slug 있으면 재사용, 없으면 신규 생성
     * 여러 source('review', 'youtube', 'apify' 등)에서 호출 가능
     *
     * @return int[] hashtag IDs
     */
    public function upsertMany(array $names, string $source = 'review'): array
    {
        $ids = [];

        foreach ($names as $name) {
            $slug = $this->toSlug($name);

            $hashtag = Hashtag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'source' => $source, 'usage_count' => 0]
            );

            $ids[] = $hashtag->id;
        }

        return $ids;
    }

    /**
     * 리뷰의 해시태그를 새 목록으로 덮어씀 (upsert + sync)
     * - 기존 태그에서 빠진 것 → usage_count 감소
     * - 새로 추가된 것 → usage_count 증가
     */
    public function syncForReview(Review $review, array $names): void
    {
        $ids    = $this->upsertMany($names);
        $oldIds = $review->hashtags()->pluck('review.hashtags.id')->toArray();

        $removed = array_diff($oldIds, $ids);
        $added   = array_diff($ids, $oldIds);

        if (!empty($removed)) {
            Hashtag::whereIn('id', $removed)->decrement('usage_count');
        }
        if (!empty($added)) {
            Hashtag::whereIn('id', $added)->increment('usage_count');
        }

        $review->hashtags()->sync($ids);
    }

    /**
     * 리뷰 삭제 시 연결된 해시태그 usage_count 정리
     */
    public function detachForReview(Review $review): void
    {
        $ids = $review->hashtags()->pluck('review.hashtags.id')->toArray();

        if (!empty($ids)) {
            Hashtag::whereIn('id', $ids)->decrement('usage_count');
        }

        $review->hashtags()->detach();
    }

    // ─── private ─────────────────────────────────────────────

    private function toSlug(string $name): string
    {
        // # 제거, 공백 제거, 소문자화 (ASCII 부분만)
        $slug = mb_strtolower(preg_replace('/\s+/', '', ltrim(trim($name), '#')));
        return mb_substr($slug, 0, 100);
    }
}

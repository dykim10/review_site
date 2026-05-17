<?php

namespace App\Services;

use App\Models\Race;
use App\Models\Review;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ReviewService
{
    /**
     * 대회별 리뷰 목록 (최신순)
     */
    public function getByRace(Race $race, int $perPage = 10): LengthAwarePaginator
    {
        return Review::with('user')
            ->where('race_id', $race->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * 해당 유저가 이미 리뷰를 작성했는지 확인 (1인 1리뷰)
     */
    public function alreadyReviewed(User $user, Race $race): bool
    {
        return Review::where('user_id', $user->id)
            ->where('race_id', $race->id)
            ->exists();
    }

    /**
     * 리뷰 작성
     */
    public function create(array $validated, User $user, Race $race): Review
    {
        return Review::create([
            'race_id'  => $race->id,
            'user_id'  => $user->id,
            'distance' => $validated['distance'],
            'rating'   => $validated['rating'],
            'content'  => $validated['content'],
        ]);
    }

    /**
     * 리뷰 수정
     */
    public function update(Review $review, array $validated): Review
    {
        $review->update([
            'distance' => $validated['distance'],
            'rating'   => $validated['rating'],
            'content'  => $validated['content'],
        ]);
        return $review->fresh();
    }

    /**
     * 리뷰 삭제
     */
    public function delete(Review $review): void
    {
        $review->delete();
    }

    /**
     * 대회 평균 평점 계산
     */
    public function avgRating(Race $race): ?float
    {
        $avg = Review::where('race_id', $race->id)->avg('rating');
        return $avg ? round($avg, 1) : null;
    }
}

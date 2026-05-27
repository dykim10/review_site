<?php

namespace App\Services;

use App\Models\Hashtag;
use App\Models\Race;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ReviewService
{
    public function __construct(private HashtagService $hashtagService) {}

    /**
     * 대회별 리뷰 목록 (최신순)
     */
    public function getByRace(Race $race, int $perPage = 10): LengthAwarePaginator
    {
        return Review::with(['user', 'hashtags'])
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
        $imagePaths = $this->uploadImages($validated['images'] ?? []);

        $review = Review::create([
            'race_id'    => $race->id,
            'user_id'    => $user->id,
            'distance'   => $validated['distance'],
            'rating'     => $validated['rating'],
            'content'    => $validated['content'],
            'image_urls' => $imagePaths,
        ]);

        $names = $this->hashtagService->parseInput($validated['hashtags'] ?? '');
        if (!empty($names)) {
            $this->hashtagService->syncForReview($review, $names);
        }

        return $review;
    }

    /**
     * 리뷰 수정
     */
    public function update(Review $review, array $validated): Review
    {
        $keepPaths  = $validated['existing_images'] ?? [];
        $oldPaths   = $review->image_urls ?? [];

        // 사용자가 삭제한 기존 이미지 파일 제거
        $this->deleteImageFiles(array_diff($oldPaths, $keepPaths));

        $newPaths  = $this->uploadImages($validated['images'] ?? []);
        $finalPaths = array_values(array_merge($keepPaths, $newPaths));

        $review->update([
            'distance'   => $validated['distance'],
            'rating'     => $validated['rating'],
            'content'    => $validated['content'],
            'image_urls' => $finalPaths,
        ]);

        $names = $this->hashtagService->parseInput($validated['hashtags'] ?? '');
        $this->hashtagService->syncForReview($review, $names);

        return $review->fresh();
    }

    /**
     * 리뷰 삭제
     */
    public function delete(Review $review): void
    {
        $this->hashtagService->detachForReview($review);
        $this->deleteImageFiles($review->image_urls ?? []);
        $review->delete();
    }

    // ─── 이미지 헬퍼 ─────────────────────────────────────────────

    private function uploadImages(array $files): array
    {
        $disk  = $this->storageDisk();
        $paths = [];
        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) continue;

            $webp = $this->convertToWebp($file);
            if ($webp !== null) {
                $filename = 'reviews/' . uniqid('', true) . '.webp';
                Storage::disk($disk)->put($filename, $webp);
                $paths[] = $filename;
            } else {
                $paths[] = $file->store('reviews', $disk);
            }
        }
        return $paths;
    }

    private function convertToWebp(UploadedFile $file): ?string
    {
        $image = @imagecreatefromstring(file_get_contents($file->getRealPath()));
        if (!$image) return null;

        ob_start();
        imagewebp($image, null, 82);
        $content = ob_get_clean();
        imagedestroy($image);

        return $content ?: null;
    }

    private function deleteImageFiles(array $paths): void
    {
        $disk = $this->storageDisk();
        foreach ($paths as $path) {
            if ($path) Storage::disk($disk)->delete($path);
        }
    }

    private function storageDisk(): string
    {
        return config('filesystems.default') === 's3' ? 's3' : 'public';
    }

    // ─── 통계 ─────────────────────────────────────────────────

    /**
     * 대회 평균 평점 계산
     */
    public function avgRating(Race $race): ?float
    {
        $avg = Review::where('race_id', $race->id)->avg('rating');
        return $avg ? round($avg, 1) : null;
    }
}

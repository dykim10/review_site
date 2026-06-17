<?php

namespace App\Services;

use App\Models\Race;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        $parsedWatch = isset($validated['parsed_watch_data'])
            ? json_decode($validated['parsed_watch_data'], true)
            : null;

        $review = Review::create([
            'race_id'          => $race->id,
            'race_edition_id'  => $validated['race_edition_id'] ?? null,
            'user_id'          => $user->id,
            'distance'         => $validated['distance'],
            'course_type'      => $validated['course_type'] ?? null,
            'finish_time'      => $validated['finish_time'] ?? null,
            'source'           => $validated['source'] ?? 'manual',
            'parsed_watch_data'=> $parsedWatch,
            'rating'           => $validated['rating'],
            'content'          => $validated['content'],
            'image_urls'       => $imagePaths,
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

        $parsedWatch = isset($validated['parsed_watch_data'])
            ? json_decode($validated['parsed_watch_data'], true)
            : null;

        $update = [
            'distance'          => $validated['distance'],
            'course_type'       => $validated['course_type'] ?? null,
            'finish_time'       => $validated['finish_time'] ?? null,
            'source'            => $validated['source'] ?? 'manual',
            'parsed_watch_data' => $parsedWatch,
            'rating'            => $validated['rating'],
            'content'           => $validated['content'],
            'image_urls'        => $finalPaths,
        ];
        if (array_key_exists('race_edition_id', $validated)) {
            $update['race_edition_id'] = $validated['race_edition_id'];
        }
        $review->update($update);

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

    // ─── 이미지 헬퍼 (CORE API → S3) ────────────────────────────

    private function uploadImages(array $files): array
    {
        $urls = [];
        $now  = now();
        $folder = "reviews/{$now->year}/{$now->format('m')}";

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) continue;
            $url = $this->uploadToS3ViaCoreApi($file, $folder);
            if ($url) $urls[] = $url;
        }
        return $urls;
    }

    private function uploadToS3ViaCoreApi(UploadedFile $file, string $folder): ?string
    {
        try {
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post(config('services.core_api.url') . '/api/photo/resize-webp', [
                    'folder' => $folder,
                ]);

            if ($response->successful()) {
                return $response->json('thumbnail_url');
            }

            Log::warning('CORE API 이미지 업로드 실패', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        } catch (ConnectionException $e) {
            Log::error('CORE API 연결 오류 (이미지 업로드): ' . $e->getMessage());
        }
        return null;
    }

    private function deleteImageFiles(array $paths): void
    {
        foreach ($paths as $url) {
            if (!$url) continue;
            try {
                Http::timeout(10)
                    ->delete(config('services.core_api.url') . '/api/s3/image', ['url' => $url]);
            } catch (ConnectionException $e) {
                Log::warning('CORE API S3 삭제 오류: ' . $e->getMessage());
            }
        }
    }

    // ─── 워치 스크린샷 파싱 ─────────────────────────────────────

    /**
     * 워치 스크린샷 → CORE API /api/parse-image → 파싱 결과 반환.
     * duration_seconds → finish_time(HH:MM:SS) 자동 변환 포함.
     */
    public function parseWatchImage(UploadedFile $file): array
    {
        $response = Http::timeout(60)
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post(config('services.core_api.url') . '/api/parse-image');

        if (!$response->successful()) {
            throw new \RuntimeException('워치 이미지 파싱에 실패했습니다.');
        }

        $data = $response->json();

        // duration_seconds → HH:MM:SS
        if (!empty($data['duration_seconds']) && is_numeric($data['duration_seconds'])) {
            $s = (int) $data['duration_seconds'];
            $data['finish_time'] = sprintf('%d:%02d:%02d', intdiv($s, 3600), intdiv($s % 3600, 60), $s % 60);
        }

        return $data;
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

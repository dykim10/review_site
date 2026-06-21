<?php

namespace App\Services;

use App\Models\Race;
use App\Models\RaceEdition;
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

    /** 특정 edition(연도)의 리뷰만 — 상세 페이지 연도 탭용. */
    public function getByEdition(RaceEdition $edition, int $perPage = 10): LengthAwarePaginator
    {
        return Review::with(['user', 'hashtags', 'raceEdition'])
            ->where('race_edition_id', $edition->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findUserReview(User $user, RaceEdition $edition): ?Review
    {
        return Review::where('user_id', $user->id)
            ->where('race_edition_id', $edition->id)
            ->first();
    }

    public function canCreateReview(RaceEdition $edition): bool
    {
        return $edition->canWriteReview();
    }

    /** @deprecated edition 단위 findUserReview 사용 */
    public function alreadyReviewed(User $user, Race $race): bool
    {
        $edition = $race->latestEdition;
        if (! $edition) {
            return false;
        }

        return $this->findUserReview($user, $edition) !== null;
    }

    public function create(array $validated, User $user, RaceEdition $edition): Review
    {
        $imagePaths = $this->uploadImages($validated['images'] ?? []);

        $parsedWatch = isset($validated['parsed_watch_data'])
            ? json_decode($validated['parsed_watch_data'], true)
            : null;

        $review = Review::updateOrCreate(
            [
                'user_id'         => $user->id,
                'race_edition_id' => $edition->id,
            ],
            [
                'distance'           => $validated['distance'],
                'course_type'        => $validated['course_type'] ?? null,
                'finish_time'        => $validated['finish_time'] ?? null,
                'source'             => $validated['source'] ?? 'manual',
                'parsed_watch_data'  => $parsedWatch,
                'rating'             => $validated['rating'],
                'content'            => $validated['content'],
                'image_urls'         => $imagePaths,
                'certificate_url'    => $validated['certificate_url'] ?? null,
            ]
        );

        $names = $this->hashtagService->parseInput($validated['hashtags'] ?? '');
        if (! empty($names)) {
            $this->hashtagService->syncForReview($review, $names);
        }

        return $review;
    }

    public function update(Review $review, array $validated): Review
    {
        $keepPaths  = $validated['existing_images'] ?? [];
        $oldPaths   = $review->image_urls ?? [];

        $this->deleteImageFiles(array_diff($oldPaths, $keepPaths));

        $newPaths   = $this->uploadImages($validated['images'] ?? []);
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

        if (array_key_exists('certificate_url', $validated)) {
            $update['certificate_url'] = $validated['certificate_url'];
        }

        $review->update($update);

        $names = $this->hashtagService->parseInput($validated['hashtags'] ?? '');
        $this->hashtagService->syncForReview($review, $names);

        return $review->fresh();
    }

    public function delete(Review $review): void
    {
        $this->hashtagService->detachForReview($review);
        $this->deleteImageFiles($review->image_urls ?? []);
        $review->delete();
    }

    private function uploadImages(array $files): array
    {
        $urls   = [];
        $now    = now();
        $folder = "reviews/{$now->year}/{$now->format('m')}";

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }
            $url = $this->uploadToS3ViaCoreApi($file, $folder);
            if ($url) {
                $urls[] = $url;
            }
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
            if (! $url) {
                continue;
            }
            try {
                Http::timeout(10)
                    ->delete(config('services.core_api.url') . '/api/s3/image', ['url' => $url]);
            } catch (ConnectionException $e) {
                Log::warning('CORE API S3 삭제 오류: ' . $e->getMessage());
            }
        }
    }

    public function parseWatchImage(UploadedFile $file): array
    {
        $response = Http::timeout(60)
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post(config('services.core_api.url') . '/api/parse-image');

        if (! $response->successful()) {
            Log::error('워치 이미지 파싱 CORE API 실패', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            $detail = $response->json('detail');
            $reason = is_string($detail) ? $detail : null;

            throw new \RuntimeException(
                $reason
                    ? "워치 이미지 파싱 실패: {$reason}"
                    : '워치 이미지 파싱에 실패했습니다. core-api(8100) 실행 여부를 확인해주세요.'
            );
        }

        $data = $response->json();

        if (! empty($data['duration_seconds']) && is_numeric($data['duration_seconds'])) {
            $s = (int) $data['duration_seconds'];
            $data['finish_time'] = sprintf('%d:%02d:%02d', intdiv($s, 3600), intdiv($s % 3600, 60), $s % 60);
        }

        return $data;
    }

    public function avgRatingForEdition(RaceEdition $edition): ?float
    {
        $avg = Review::where('race_edition_id', $edition->id)->avg('rating');

        return $avg ? round($avg, 1) : null;
    }
}

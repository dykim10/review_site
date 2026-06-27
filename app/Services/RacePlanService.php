<?php

namespace App\Services;

use App\Models\Race;
use App\Models\RaceCourse;
use App\Models\RaceEdition;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RacePlanService
{
    private const MAX_TRAINING_IMAGES = 5;

    private const ALLOWED_IMAGE_MIMES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    public function generate(
        RaceEdition $edition,
        int $userId,
        string $courseType,
        string $goalTime,
        string $trainingStatus = 'normal',
        ?float $recentLongKm = null,
        ?string $recent10kTime = null,
        array $parsedImageLogs = [],
    ): array {
        try {
            $response = Http::timeout(120)
                ->post(config('services.core_api.url') . '/api/race-plan/generate', [
                    'race_edition_id'  => $edition->id,
                    'user_id'          => $userId,
                    'course_type'      => $courseType,
                    'goal_time'        => $goalTime,
                    'training_status'  => $trainingStatus,
                    'recent_long_km'   => $recentLongKm,
                    'recent_10k_time'  => $recent10kTime,
                    'parsed_image_logs'=> $parsedImageLogs,
                    'live'             => app()->environment('production'),
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('[RacePlan] CORE API 오류', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('레이스 플랜 생성에 실패했습니다. (' . $response->status() . ')');
        } catch (ConnectionException $e) {
            Log::error('[RacePlan] CORE API 연결 오류: ' . $e->getMessage());
            throw new \RuntimeException('CORE API에 연결할 수 없습니다.');
        }
    }

    /**
     * 레이스 플랜용 1회성 워치/앱 스크린샷 파싱 (S3 저장 없음).
     *
     * @param  list<UploadedFile>  $files
     * @return list<array<string, mixed>>
     */
    public function parseEphemeralTrainingImages(array $files): array
    {
        $files = array_slice(array_values($files), 0, self::MAX_TRAINING_IMAGES);
        $parsed = [];
        $coreUrl = rtrim(config('services.core_api.url', 'http://localhost:8100'), '/');

        foreach ($files as $idx => $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $mime = $file->getMimeType() ?: '';
            if (! in_array(strtolower($mime), self::ALLOWED_IMAGE_MIMES, true)) {
                throw new \RuntimeException('이미지 파일만 업로드할 수 있습니다. (JPEG, PNG, GIF, WebP)');
            }

            if ($file->getSize() > 10 * 1024 * 1024) {
                throw new \RuntimeException('이미지는 파일당 10MB 이하여야 합니다.');
            }

            try {
                $response = Http::timeout(90)
                    ->attach(
                        'file',
                        file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName(),
                        ['Content-Type' => $mime]
                    )
                    ->post("{$coreUrl}/api/parse-image/ephemeral");
            } catch (ConnectionException $e) {
                throw new \RuntimeException('CORE API에 연결할 수 없습니다. core-api(8100) 실행 여부를 확인하세요.');
            }

            if (! $response->successful()) {
                $detail = $response->json('detail');
                $reason = is_string($detail) ? $detail : null;
                $n = $idx + 1;

                throw new \RuntimeException(
                    $reason
                        ? "훈련 이미지 {$n}번 파싱 실패: {$reason}"
                        : "훈련 이미지 {$n}번 파싱에 실패했습니다."
                );
            }

            $data = $response->json();
            if (is_array($data)) {
                $parsed[] = $data;
            }
        }

        return $parsed;
    }

    public function hasOfficialGpx(RaceEdition $edition, string $courseType): bool
    {
        return RaceCourse::where('race_edition_id', $edition->id)
            ->where('course_type', $courseType)
            ->whereNotNull('gpx_url')
            ->exists();
    }

    public function availableEditions(Race $race): \Illuminate\Database\Eloquent\Collection
    {
        return $race->editions()->orderByDesc('year')->get(['id', 'year', 'race_date', 'name', 'status']);
    }

    public function courseTypesFor(Race $race): array
    {
        $distances = $race->distances ?? [];
        $map       = [];
        foreach ($distances as $d) {
            $d = trim($d);
            if (in_array($d, ['풀', '풀마라톤', '42', '42.195km', 'FULL', 'Full'])) {
                $map['FULL'] = '풀마라톤 (42.195km)';
            } elseif (in_array($d, ['하프', '하프마라톤', '21', '21km', 'HALF', 'Half'])) {
                $map['HALF'] = '하프마라톤 (21km)';
            } elseif (in_array($d, ['10K', '10km', '10k'])) {
                $map['10K'] = '10K (10km)';
            }
        }
        if (empty($map)) {
            $map = ['FULL' => '풀마라톤 (42.195km)', 'HALF' => '하프마라톤 (21km)', '10K' => '10K (10km)'];
        }

        return $map;
    }
}

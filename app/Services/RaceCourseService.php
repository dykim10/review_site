<?php

namespace App\Services;

use App\Models\RaceCourse;
use App\Models\RaceEdition;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RaceCourseService
{
    private string $coreApiUrl;

    public function __construct()
    {
        $this->coreApiUrl = rtrim(config('services.core_api.url', 'http://localhost:8100'), '/');
    }

    /**
     * GPX 파일을 CORE API를 통해 S3에 업로드하고 race_courses 에 저장한다.
     * UNIQUE(race_edition_id, course_type) 이므로 기존 레코드가 있으면 덮어쓴다.
     */
    public function uploadAndSave(
        RaceEdition $edition,
        string $courseType,
        UploadedFile $gpxFile,
        array $extra = []
    ): RaceCourse {
        $uploaded = $this->uploadToS3($gpxFile, $edition->id, $courseType);

        $elevationProfile = $this->fetchElevationProfile($uploaded['gpx_url']);

        $payload = array_merge([
            'race_edition_id' => $edition->id,
            'course_type'     => strtoupper($courseType),
            'gpx_url'         => $uploaded['gpx_url'],
            'elevation_data'  => $elevationProfile,
            'segments'        => $uploaded['segments'],
            'coordinates'     => $uploaded['coordinates'],
            'markers'         => $uploaded['markers'],
            'source'          => $extra['source']      ?? 'manual',
            'is_certified'    => $extra['is_certified'] ?? false,
            'certified_at'    => $extra['certified_at'] ?? null,
        ]);

        if (!$elevationProfile) {
            Log::warning('고저도 프로파일 생성 실패 — gpx_url은 저장됨', [
                'race_edition_id' => $edition->id,
                'course_type'     => $courseType,
            ]);
        }

        $course = RaceCourse::updateOrCreate(
            ['race_edition_id' => $edition->id, 'course_type' => strtoupper($courseType)],
            $payload
        );

        return $course;
    }

    public function elevationProfileGenerated(?RaceCourse $course): bool
    {
        if (! $course) {
            return false;
        }
        $data = $course->elevation_data;

        return is_array($data) && ! empty($data['points']);
    }

    /**
     * 기존 GPX(gpx_url)로 CORE 고저도 프로파일 재생성 → elevation_data 갱신.
     * (구 gpx_service 요약 형식 total_gain_m 만 있는 row 백필용)
     */
    public function regenerateElevationProfile(RaceCourse $course): RaceCourse
    {
        if (! $course->gpx_url) {
            throw new \RuntimeException('GPX URL이 없습니다.');
        }

        $profile = $this->fetchElevationProfile($course->gpx_url);
        if (! $profile) {
            throw new \RuntimeException('CORE 고저도 프로파일 생성에 실패했습니다. core-api(8100) 및 S3 GPX를 확인하세요.');
        }

        $course->update(['elevation_data' => $profile]);

        return $course->fresh();
    }

    /**
     * 메타데이터 수정 및(또는) GPX 파일 교체.
     * edition/course_type 은 변경하지 않는다 (S3 키·UNIQUE 제약).
     */
    public function update(RaceCourse $course, array $extra, ?UploadedFile $gpxFile = null): RaceCourse
    {
        if ($gpxFile) {
            $edition = $course->raceEdition ?? RaceEdition::findOrFail($course->race_edition_id);

            return $this->uploadAndSave($edition, $course->course_type, $gpxFile, $extra);
        }

        $course->update([
            'source'       => $extra['source'] ?? $course->source,
            'is_certified' => (bool) ($extra['is_certified'] ?? false),
            'certified_at' => $extra['certified_at'] ?? null,
        ]);

        return $course->fresh();
    }

    public function delete(RaceCourse $course): void
    {
        if ($course->gpx_url) {
            $this->deleteFromS3($course->gpx_url);
        }
        $course->delete();
    }

    /**
     * @return array{gpx_url: string, elevation_data: ?array, segments: ?array, coordinates: ?array, markers: ?array}
     */
    private function uploadToS3(UploadedFile $file, int $editionId, string $courseType): array
    {
        $response = Http::timeout(60)
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post("{$this->coreApiUrl}/api/gpx/upload", [
                'race_edition_id' => $editionId,
                'course_type'     => strtoupper($courseType),
            ]);

        if (!$response->successful()) {
            Log::error('GPX S3 업로드 실패', ['status' => $response->status(), 'body' => $response->body()]);

            $detail = $response->json('detail');
            $reason = is_string($detail) ? $detail : null;

            throw new \RuntimeException(
                $reason
                    ? "GPX 파일 업로드 실패: {$reason}"
                    : 'GPX 파일 업로드에 실패했습니다. core-api(8100) 실행 및 AWS 설정을 확인해주세요.'
            );
        }

        $gpxUrl = $response->json('gpx_url');
        if (!$gpxUrl) {
            throw new \RuntimeException('GPX 업로드 응답에 URL이 없습니다.');
        }

        if (!$response->json('elevation_data')) {
            Log::warning('GPX 파싱(구간/좌표) 일부 실패 — gpx_url만 저장될 수 있음', [
                'race_edition_id' => $editionId,
                'course_type'     => $courseType,
            ]);
        }

        return [
            'gpx_url'        => $gpxUrl,
            'elevation_data' => $response->json('elevation_data'),
            'segments'       => $response->json('segments'),
            'coordinates'    => $response->json('coordinates'),
            'markers'        => $response->json('markers'),
        ];
    }

    private function fetchElevationProfile(string $gpxUrl, float $intervalM = 100.0): ?array
    {
        $key = $this->gpxUrlToS3Key($gpxUrl);
        if (!$key) {
            return null;
        }

        try {
            $response = Http::timeout(60)->post("{$this->coreApiUrl}/api/course/elevation", [
                'gpx_s3_key' => $key,
                'interval_m' => $intervalM,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && ! empty($data['points'])) {
                    return $data;
                }
            }

            Log::warning('CORE 고저도 프로파일 응답 오류', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            $detail = $response->json('detail');
            if (is_string($detail)) {
                throw new \RuntimeException("CORE API 오류 ({$response->status()}): {$detail}");
            }
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::warning('CORE 고저도 프로파일 호출 실패', ['error' => $e->getMessage()]);

            if (str_contains($e->getMessage(), 'Failed to connect') || str_contains($e->getMessage(), 'Connection refused')) {
                throw new \RuntimeException(
                    "core-api({$this->coreApiUrl})에 연결할 수 없습니다. core-api를 8100 포트에서 실행했는지 확인하세요."
                );
            }

            throw new \RuntimeException('CORE 호출 실패: '.$e->getMessage());
        }

        return null;
    }

    private function gpxUrlToS3Key(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (! is_string($path) || $path === '') {
            return null;
        }

        $key = ltrim($path, '/');

        return str_starts_with($key, 'race-courses/') ? $key : null;
    }

    private function deleteFromS3(string $url): void
    {
        try {
            Http::timeout(10)->delete("{$this->coreApiUrl}/api/gpx/delete", ['url' => $url]);
        } catch (\Throwable $e) {
            Log::warning('GPX S3 삭제 실패 (DB 삭제는 진행)', ['url' => $url, 'error' => $e->getMessage()]);
        }
    }
}

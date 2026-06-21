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

        $payload = array_merge([
            'race_edition_id' => $edition->id,
            'course_type'     => strtoupper($courseType),
            'gpx_url'         => $uploaded['gpx_url'],
            'elevation_data'  => $uploaded['elevation_data'],
            'segments'        => $uploaded['segments'],
            'coordinates'     => $uploaded['coordinates'],
            'markers'         => $uploaded['markers'],
            'source'          => $extra['source']      ?? 'manual',
            'is_certified'    => $extra['is_certified'] ?? false,
            'certified_at'    => $extra['certified_at'] ?? null,
        ]);

        $course = RaceCourse::updateOrCreate(
            ['race_edition_id' => $edition->id, 'course_type' => strtoupper($courseType)],
            $payload
        );

        return $course;
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
            Log::warning('GPX 고도 파싱 실패 — gpx_url만 저장 (분석 시 "상세 데이터 없음"으로 표시됨)', [
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

    private function deleteFromS3(string $url): void
    {
        try {
            Http::timeout(10)->delete("{$this->coreApiUrl}/api/gpx/delete", ['url' => $url]);
        } catch (\Throwable $e) {
            Log::warning('GPX S3 삭제 실패 (DB 삭제는 진행)', ['url' => $url, 'error' => $e->getMessage()]);
        }
    }
}

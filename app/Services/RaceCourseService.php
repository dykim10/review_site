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
        $gpxUrl = $this->uploadToS3($gpxFile, $edition->id, $courseType);

        $payload = array_merge([
            'race_edition_id' => $edition->id,
            'course_type'     => strtoupper($courseType),
            'gpx_url'         => $gpxUrl,
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

    private function uploadToS3(UploadedFile $file, int $editionId, string $courseType): string
    {
        $response = Http::timeout(30)
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post("{$this->coreApiUrl}/api/gpx/upload", [
                'race_edition_id' => $editionId,
                'course_type'     => strtoupper($courseType),
            ]);

        if (!$response->successful()) {
            Log::error('GPX S3 업로드 실패', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('GPX 파일 업로드에 실패했습니다. 잠시 후 다시 시도해주세요.');
        }

        return $response->json('gpx_url');
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

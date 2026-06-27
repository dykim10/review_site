<?php

namespace App\Console\Commands;

use App\Models\RaceCourse;
use App\Services\RaceCourseService;
use Illuminate\Console\Command;

class BackfillRaceCourseElevation extends Command
{
    protected $signature = 'race-courses:backfill-elevation
                            {--edition= : race_edition_id (미지정 시 points 없는 전체)}
                            {--dry-run : 대상만 출력}';

    protected $description = 'GPX 있는 race_courses에 CORE 고저도 프로파일(points[]) 백필';

    public function handle(RaceCourseService $service): int
    {
        $query = RaceCourse::query()
            ->whereNotNull('gpx_url')
            ->when($this->option('edition'), fn ($q, $id) => $q->where('race_edition_id', (int) $id));

        $candidates = $query->get()->filter(fn (RaceCourse $c) => ! $service->elevationProfileGenerated($c));

        if ($candidates->isEmpty()) {
            $this->info('백필 대상 없음 (이미 points[] 있거나 gpx_url 없음)');

            return self::SUCCESS;
        }

        $this->info("백필 대상: {$candidates->count()}건");
        foreach ($candidates as $course) {
            $this->line("  edition={$course->race_edition_id} {$course->course_type} gpx={$course->gpx_url}");
        }

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        $coreUrl = rtrim(config('services.core_api.url', 'http://localhost:8100'), '/');
        try {
            $ping = \Illuminate\Support\Facades\Http::timeout(3)->get("{$coreUrl}/docs");
            if (! $ping->successful() && ! $ping->clientError()) {
                $this->error("core-api({$coreUrl}) 응답 없음. 먼저 core-api를 실행하세요.");
                $this->line('  cd core-api && uvicorn main:app --reload --port 8100');

                return self::FAILURE;
            }
        } catch (\Throwable $e) {
            $this->error("core-api({$coreUrl}) 연결 실패: {$e->getMessage()}");
            $this->line('  cd core-api && uvicorn main:app --reload --port 8100');

            return self::FAILURE;
        }

        if (config('database.default') === 'pgsql_live' && filter_var(env('DB_LIVE_READONLY', 'true'), FILTER_VALIDATE_BOOLEAN)) {
            $this->error('LIVE READ-ONLY 모드입니다. 백필하려면 .env 에 DB_LIVE_READONLY=false 후 config:clear');

            return self::FAILURE;
        }

        $ok = 0;
        $fail = 0;
        foreach ($candidates as $course) {
            try {
                $service->regenerateElevationProfile($course);
                $fresh = $course->fresh();
                $pts = count($fresh->elevation_data['points'] ?? []);
                $this->info("  OK edition={$course->race_edition_id} {$course->course_type} points={$pts}");
                $ok++;
            } catch (\Throwable $e) {
                $this->error("  FAIL edition={$course->race_edition_id} {$course->course_type}: {$e->getMessage()}");
                $fail++;
            }
        }

        $this->newLine();
        $this->info("완료: 성공 {$ok} · 실패 {$fail}");

        return $fail > 0 ? self::FAILURE : self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ResetDerivedReviewData extends Command
{
    protected $signature = 'review:reset-derived-data
                            {--dry-run : 삭제 건수만 출력}
                            {--skip-s3 : S3 race-courses 정리 생략}
                            {--force : 확인 없이 실행}';

    protected $description = 'races 베이스 카탈로그만 유지하고 파생 데이터(editions·reviews 등) 전량 삭제';

    /** @var list<string> FK 자식 → 부모 순서 */
    private array $tables = [
        'review.race_weather_cases',
        'review.review_hashtags',
        'review.reviews',
        'review.race_weather',
        'review.race_courses',
        'review.youtube_cache',
        'review.instagram_cache',
        'review.race_plans',
        'review.edition_feedback',
        'review.race_stats',
        'review.race_editions',
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if (! Schema::hasTable('review.races')) {
            $this->error('review.races 테이블이 없습니다.');

            return 1;
        }

        $racesBefore = (int) DB::table('review.races')->count();
        $this->line("races: {$racesBefore} (유지)");

        foreach ($this->tables as $table) {
            if (! $this->tableExists($table)) {
                $this->line("{$table}: (테이블 없음 — skip)");

                continue;
            }

            $count = (int) DB::table($table)->count();
            $this->line("{$table}: {$count}" . ($dryRun ? ' (will delete)' : ''));
        }

        if ($dryRun) {
            $this->info('dry-run 완료. 실행: php artisan review:reset-derived-data --force');

            return 0;
        }

        if (! $this->option('force') && ! $this->confirm('파생 데이터를 전량 삭제합니다. 계속할까요?')) {
            $this->warn('취소됨.');

            return 1;
        }

        foreach ($this->tables as $table) {
            if (! $this->tableExists($table)) {
                continue;
            }
            DB::table($table)->delete();
        }

        $racesAfter = (int) DB::table('review.races')->count();
        if ($racesAfter !== $racesBefore) {
            $this->error("races 건수 변경 감지: {$racesBefore} → {$racesAfter}. 중단.");

            return 1;
        }

        if (! $this->option('skip-s3') && config('filesystems.default') === 's3') {
            $this->purgeS3RaceCourses();
        }

        $this->info("완료. races={$racesAfter} 유지, 파생 테이블 비움.");

        return 0;
    }

    private function tableExists(string $qualified): bool
    {
        [$schema, $table] = explode('.', $qualified, 2);

        return Schema::hasTable("{$schema}.{$table}");
    }

    private function purgeS3RaceCourses(): void
    {
        try {
            $disk  = Storage::disk('s3');
            $files = $disk->allFiles('race-courses');
            $this->line('S3 race-courses/: ' . count($files) . ' objects');

            foreach ($files as $path) {
                $disk->delete($path);
            }
        } catch (\Throwable $e) {
            $this->warn('S3 정리 실패: ' . $e->getMessage());
        }
    }
}

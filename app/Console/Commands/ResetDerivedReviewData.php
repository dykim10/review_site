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
                            {--purge-races : 파생 삭제 후 review.races 전량 DELETE (카탈로그 초기화)}
                            {--force : 확인 없이 실행}';

    protected $description = '파생 데이터(editions·reviews 등) 삭제. --purge-races 시 races 카탈로그도 비움';

    /** @var list<string> FK 자식 → 부모 순서 (review 스키마) */
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

        $purgeRaces = (bool) $this->option('purge-races');
        $racesBefore = (int) DB::table('review.races')->count();
        $this->line('races: '.$racesBefore.($purgeRaces ? ' (will purge)' : ' (유지)'));
        $this->line('── 삭제 전 ──');
        $this->printTableCounts($dryRun ? 'will delete' : null);

        if ($dryRun) {
            $hint = $purgeRaces
                ? 'php artisan review:reset-derived-data --force --skip-s3 --purge-races'
                : 'php artisan review:reset-derived-data --force --skip-s3';
            $this->info("dry-run 완료. 실행: {$hint}");

            return 0;
        }

        if (! $this->option('force') && ! $this->confirm(
            $purgeRaces
                ? '파생 데이터 + races 카탈로그를 전량 삭제합니다. 계속할까요?'
                : '파생 데이터를 전량 삭제합니다. 계속할까요?'
        )) {
            $this->warn('취소됨.');

            return 1;
        }

        foreach ($this->tables as $table) {
            if (! $this->tableExists($table)) {
                continue;
            }
            // PostgreSQL review 스키마 — query builder 테이블명 이슈 방지
            DB::statement("DELETE FROM {$table}");
        }

        if ($purgeRaces) {
            DB::statement('DELETE FROM review.races');
            DB::statement('ALTER SEQUENCE IF EXISTS review.races_id_seq RESTART WITH 1');
        }

        $racesAfter = (int) DB::table('review.races')->count();
        if (! $purgeRaces && $racesAfter !== $racesBefore) {
            $this->error("races 건수 변경 감지: {$racesBefore} → {$racesAfter}. 중단.");

            return 1;
        }

        if ($purgeRaces && $racesAfter !== 0) {
            $this->error("races purge 실패: {$racesAfter}건 남음.");

            return 1;
        }

        $this->line('── 삭제 후 ──');
        $failed = false;
        foreach ($this->tables as $table) {
            if (! $this->tableExists($table)) {
                continue;
            }
            $remaining = (int) DB::table($table)->count();
            $this->line("{$table}: {$remaining}");
            if ($remaining > 0) {
                $failed = true;
            }
        }

        $this->line('races: '.$racesAfter);

        if ($failed) {
            $this->error('일부 파생 테이블이 비워지지 않았습니다. FK/권한을 확인하세요.');

            return 1;
        }

        if (! $this->option('skip-s3') && config('filesystems.default') === 's3') {
            $this->purgeS3RaceCourses();
        }

        $msg = $purgeRaces
            ? "완료. races=0, 파생 테이블 0건 확인. → WA sync → seed-pilot 순서"
            : "완료. races={$racesAfter} 유지, 파생 테이블 0건 확인.";
        $this->info($msg);

        return 0;
    }

    private function printTableCounts(?string $suffix): void
    {
        foreach ($this->tables as $table) {
            if (! $this->tableExists($table)) {
                $this->line("{$table}: (테이블 없음 — skip)");

                continue;
            }

            $count = (int) DB::table($table)->count();
            $tag   = $suffix ? " ({$suffix})" : '';
            $this->line("{$table}: {$count}{$tag}");
        }
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

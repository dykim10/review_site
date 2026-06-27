<?php

namespace App\Console\Commands;

use App\Services\PilotRaceMergeService;
use Illuminate\Console\Command;

class MergePilotRaces extends Command
{
    protected $signature = 'review:merge-pilot-races
                            {--dry-run : 변경 없이 계획만 출력 (기본)}
                            {--force : 실제 DB 반영}';

    protected $description = 'Pilot orphan races(#283~286) edition을 WA 카탈로그 races로 이전 — S3 gpx_url(edition_id) 유지';

    public function handle(PilotRaceMergeService $merge): int
    {
        $dryRun = ! $this->option('force');

        if ($dryRun) {
            $this->warn('DRY-RUN — 반영하려면 --force 를 사용하세요.');
        } else {
            if (! $this->option('no-interaction') && ! $this->confirm('Pilot orphan → WA 카탈로그 merge 를 실행합니다. 계속할까요?')) {
                $this->info('취소됨.');

                return self::SUCCESS;
            }
        }

        $lines = $merge->merge($dryRun);

        foreach ($lines as $line) {
            $this->line($line);
        }

        $this->newLine();
        $this->info($dryRun
            ? '완료 (dry-run). --force 로 실행하세요.'
            : 'Merge 완료. 공개 URL 예: /races/{catalog_id}/editions/{edition_id}');

        return self::SUCCESS;
    }
}

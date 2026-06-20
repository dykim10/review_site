<?php

namespace App\Console\Commands;

use App\Services\WaLabelSyncService;
use Illuminate\Console\Command;

class WaLabelSyncCommand extends Command
{
    protected $signature = 'review:wa-label-sync
                            {year : 시즌 (예: 2024)}
                            {--translate : 한국어 번역 (Haiku)}
                            {--organiser : 주최/공식 URL 수집}';

    protected $description = 'WA Label Road Races 시즌 sync (core-api, CLI — nginx 타임아웃 회피)';

    public function handle(WaLabelSyncService $waLabelSync): int
    {
        $year = (int) $this->argument('year');

        if ($year < 2018 || $year > 2035) {
            $this->error('year는 2018~2035 범위여야 합니다.');

            return 1;
        }

        $translate = (bool) $this->option('translate');
        $organiser = (bool) $this->option('organiser');

        $this->info("WA Label sync 시작 — season {$year} (translate=".($translate ? 'Y' : 'N').', organiser='.($organiser ? 'Y' : 'N').')');

        try {
            $result = $waLabelSync->syncSeason($year, $translate, $organiser);
        } catch (\Throwable $e) {
            $this->error('동기화 실패: '.$e->getMessage());

            return 1;
        }

        $this->table(
            ['항목', '건수'],
            collect($result)->map(fn ($v, $k) => [$k, $v])->values()->all()
        );

        $this->info('완료.');

        return 0;
    }
}

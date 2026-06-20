<?php

namespace App\Console\Commands;

use App\Models\RaceEdition;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateEditionStatus extends Command
{
    protected $signature = 'editions:update-status {--dry-run : 변경 건수만 출력}';

    protected $description = 'race_date 경과 edition 종료 전환 + 다음 연도 edition 자동 생성';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $today  = Carbon::today()->toDateString();
        $now    = Carbon::now();

        $toEnd = RaceEdition::query()
            ->whereNotNull('race_date')
            ->whereDate('race_date', '<', $today)
            ->where('status', '!=', 'ended');

        $count = (clone $toEnd)->count();

        if ($dryRun) {
            $this->info("종료 전환 대상: {$count}건");
            foreach ((clone $toEnd)->get(['id', 'name', 'year', 'race_date']) as $ed) {
                $this->line("  #{$ed->id} {$ed->name} ({$ed->year}) date={$ed->race_date}");
            }
        } else {
            $endedIds = (clone $toEnd)->pluck('id');
            $updated  = $toEnd->update([
                'status'           => 'ended',
                'is_review_open'   => true,
                'updated_at'       => $now,
            ]);
            $this->info("종료 전환: {$updated}건");

            $created = 0;
            foreach ($endedIds as $editionId) {
                if ($this->createNextYearEdition($editionId)) {
                    $created++;
                }
            }
            $this->info("다음 연도 edition 생성: {$created}건");
        }

        return 0;
    }

    private function createNextYearEdition(int $editionId): bool
    {
        $ed = RaceEdition::find($editionId);
        if (! $ed?->race_id) {
            return false;
        }

        $nextYear = $ed->year + 1;

        if (RaceEdition::where('race_id', $ed->race_id)->where('year', $nextYear)->exists()) {
            return false;
        }

        RaceEdition::create([
            'race_id'          => $ed->race_id,
            'name'             => $ed->name,
            'year'             => $nextYear,
            'race_date'        => null,
            'race_time'        => null,
            'location'         => $ed->location,
            'city'             => $ed->city,
            'is_domestic'      => $ed->is_domestic,
            'country'          => $ed->country,
            'entry_fee'        => null,
            'reg_start'        => null,
            'reg_end'          => null,
            'status'           => 'upcoming',
            'is_review_open'   => false,
            'is_active'        => true,
            'weather_stn_id'   => $ed->weather_stn_id,
        ]);

        return true;
    }
}

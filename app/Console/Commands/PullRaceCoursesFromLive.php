<?php

namespace App\Console\Commands;

use App\Models\RaceCourse;
use Illuminate\Console\Command;

class PullRaceCoursesFromLive extends Command
{
    protected $signature = 'race-courses:pull-from-live
                            {edition : race_edition_id}
                            {--dry-run : 복사 대상만 출력}';

    protected $description = '[로컬 전용] LIVE DB(pgsql_live) race_courses → TEST DB(pgsql) 복사';

    public function handle(): int
    {
        if (! app()->environment('local')) {
            $this->error('로컬(APP_ENV=local)에서만 사용할 수 있습니다.');

            return self::FAILURE;
        }

        $editionId = (int) $this->argument('edition');
        $liveRows = RaceCourse::on('pgsql_live')->where('race_edition_id', $editionId)->get();

        if ($liveRows->isEmpty()) {
            $this->warn("LIVE에 race_edition_id={$editionId} 코스 데이터 없음");

            return self::FAILURE;
        }

        $this->info("LIVE → TEST 복사 대상: {$liveRows->count()}건 (edition {$editionId})");
        foreach ($liveRows as $row) {
            $coords = is_array($row->coordinates) ? count($row->coordinates) : 0;
            $pts = count($row->elevation_data['points'] ?? []);
            $this->line("  {$row->course_type} coords={$coords} elev_pts={$pts}");
        }

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        foreach ($liveRows as $row) {
            RaceCourse::on('pgsql')->updateOrCreate(
                [
                    'race_edition_id' => $row->race_edition_id,
                    'course_type'     => $row->course_type,
                ],
                $row->only([
                    'gpx_url', 'elevation_data', 'segments', 'coordinates', 'markers',
                    'source', 'is_certified', 'certified_at',
                ])
            );
        }

        $this->info('TEST DB(pgsql)에 반영 완료. DB_TARGET=test 로 페이지를 확인하세요.');

        return self::SUCCESS;
    }
}

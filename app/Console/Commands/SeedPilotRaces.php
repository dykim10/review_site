<?php

namespace App\Console\Commands;

use App\Models\EditionFeedback;
use App\Models\Race;
use App\Models\RaceEdition;
use App\Models\RaceWeather;
use App\Models\Review;
use App\Models\User;
use App\Services\PilotEditionService;
use App\Services\WeatherCaseService;
use App\Services\WeatherService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedPilotRaces extends Command
{
    protected $signature = 'review:seed-pilot
                            {--from=2020 : edition 시작 연도}
                            {--to= : edition 종료 연도 (기본: 올해)}
                            {--gpx-year=2025 : GPX 스텁 연도}
                            {--smoke : 스모크용 샘플 review/feedback}
                            {--skip-weather : ASOS 생략}
                            {--force : smoke만 보완}
                            {--repair : 메타만 갱신 (catalog 반영)}';

    protected $description = 'TASK-16: pilot 4대회 races + race_editions (PilotEditionService)';

    public function handle(PilotEditionService $pilotEditions, WeatherService $weatherService): int
    {
        $fromYear = (int) $this->option('from');
        $toYear   = (int) ($this->option('to') ?: date('Y'));
        $gpxYear  = (int) $this->option('gpx-year');

        if ($fromYear > $toYear) {
            $this->error('--from 은 --to 이하여야 합니다.');

            return 1;
        }

        $years = range($fromYear, $toYear);

        if ($this->option('repair')) {
            return $this->repair($pilotEditions, $years);
        }

        if ($this->option('force')) {
            if ($this->option('smoke')) {
                $this->runSmoke($gpxYear);
            }
            $this->info('Pilot seed (--force) 완료.');

            return 0;
        }

        $this->info('Pilot provision — '.implode(', ', $years));

        $result = $pilotEditions->provision($years, $gpxYear);

        if (! $this->option('skip-weather')) {
            foreach ($result['resultRows'] as $row) {
                if ($row['year'] !== $gpxYear || $row['status'] !== 'ended' || ! $row['race_date']) {
                    continue;
                }
                $edition = RaceEdition::find($row['edition_id']);
                if ($edition) {
                    try {
                        $weatherService->getForEdition($edition);
                        $this->line("  [weather] {$row['name']} {$row['year']}");
                    } catch (\Throwable $e) {
                        $this->warn("  [weather] {$row['name']} 실패: {$e->getMessage()}");
                    }
                }
            }
        }

        $this->table(
            ['대회', '연도', 'edition', '날짜', '출처', '상태', 'action'],
            collect($result['resultRows'])->map(fn ($r) => [
                $r['name'], $r['year'], $r['edition_id'],
                $r['race_date'] ?? '미정', $r['date_source'], $r['status'], $r['action'],
            ])->all()
        );

        $this->info("완료 — 신규 {$result['created']} / 갱신 {$result['updated']}");

        if ($this->option('smoke')) {
            $this->runSmoke($gpxYear);
        }

        return 0;
    }

    /** @param list<int> $years */
    private function repair(PilotEditionService $pilotEditions, array $years): int
    {
        $this->info('Pilot repair — provision upsert');

        $result = $pilotEditions->provision($years, null);
        $this->info("repair 완료 — 갱신 {$result['updated']} / 신규 {$result['created']}");

        return 0;
    }

    private function runSmoke(int $gpxYear): void
    {
        $seoulRace = Race::where('name', '서울국제마라톤')->first();
        $ended = $seoulRace
            ? RaceEdition::where('race_id', $seoulRace->id)->where('year', $gpxYear)->first()
            : null;
        $upcoming = $seoulRace
            ? RaceEdition::where('race_id', $seoulRace->id)->where('year', (int) date('Y'))->where('status', 'upcoming')->first()
            : null;

        if (! $ended) {
            $this->warn('  [smoke] 서울 ended edition 없음');

            return;
        }

        $this->seedSmokeData($ended, $upcoming);
    }

    private function seedSmokeData(RaceEdition $endedEdition, ?RaceEdition $upcomingEdition): void
    {
        $user = User::query()->orderBy('id')->first();
        if (! $user) {
            $this->warn('  [smoke] users 없음');

            return;
        }

        RaceWeather::updateOrCreate(
            ['race_edition_id' => $endedEdition->id],
            [
                'stn_id'            => $endedEdition->weather_stn_id ?? 108,
                'temperature'       => 12.5,
                'humidity'          => 55,
                'wind_speed'        => 2.1,
                'wind_direction'    => 16,
                'weather_condition' => '맑음',
                'fetched_at'        => now(),
            ]
        );

        $review = Review::firstOrCreate(
            [
                'user_id'         => $user->id,
                'race_edition_id' => $endedEdition->id,
            ],
            [
                'rating'      => 5,
                'content'     => 'Pilot smoke test — 코스·운영 모두 만족스러웠습니다.',
                'course_type' => 'FULL',
                'finish_time' => '03:45:00',
            ]
        );

        app(WeatherCaseService::class)->upsertFromReview(
            $review->fresh(['raceEdition.race', 'raceEdition.weather'])
        );

        if ($upcomingEdition) {
            EditionFeedback::firstOrCreate(
                [
                    'user_id'         => $user->id,
                    'race_edition_id' => $upcomingEdition->id,
                ],
                [
                    'content'  => 'Pilot smoke — 코스 편성 기대합니다.',
                    'category' => 'course',
                ]
            );
        }

        $this->line('  [smoke] review/feedback 보완');
    }
}

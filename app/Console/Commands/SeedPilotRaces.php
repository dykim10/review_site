<?php

namespace App\Console\Commands;

use App\Models\EditionFeedback;
use App\Models\Race;
use App\Models\RaceCourse;
use App\Models\RaceWeather;
use App\Models\RaceEdition;
use App\Models\Review;
use App\Models\User;
use App\Services\WeatherCaseService;
use App\Services\WeatherService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SeedPilotRaces extends Command
{
    protected $signature = 'review:seed-pilot
                            {--smoke : 스모크용 샘플 review/feedback 생성}
                            {--skip-weather : ASOS 수집 생략}
                            {--force : 기존 edition 있어도 추가 생성}';

    protected $description = 'TASK-16: WA 인증 4대회 pilot edition + GPX + (선택) 스모크 데이터';

    /** @var list<array{race_id:int,key:string,name:string,city:string,location:string,weather_stn_id:int}> */
    private array $pilots = [
        ['race_id' => 2403, 'key' => 'seoul', 'name' => '서울국제마라톤', 'city' => 'Seoul', 'location' => '서울', 'weather_stn_id' => 108],
        ['race_id' => 2430, 'key' => 'daegu', 'name' => '대구마라톤', 'city' => 'Daegu', 'location' => '대구', 'weather_stn_id' => 143],
        ['race_id' => 2701, 'key' => 'gyeongju', 'name' => '경주마라톤', 'city' => '경주', 'location' => '경주', 'weather_stn_id' => 136],
        ['race_id' => 2558, 'key' => 'gunsan', 'name' => '군산 새만금 국제 마라톤', 'city' => 'Gunsan', 'location' => '군산', 'weather_stn_id' => 146],
    ];

    public function handle(WeatherService $weatherService): int
    {
        $racesCount = (int) DB::table('review.races')->count();
        $editionsCount = (int) DB::table('review.race_editions')->count();

        if ($editionsCount > 0 && ! $this->option('force')) {
            $this->warn("race_editions={$editionsCount} — TASK-02 초기화 후 실행 권장.");
            if (! $this->confirm('기존 edition이 있습니다. 계속할까요?')) {
                return 1;
            }
        }

        if ($editionsCount > 0 && $this->option('force')) {
            $this->line("기존 edition {$editionsCount}건 — smoke 데이터만 보완합니다.");
            if ($this->option('smoke')) {
                $seoul = RaceEdition::where('race_id', 2403)->where('year', 2025)->first();
                $upcoming = RaceEdition::where('status', 'upcoming')->whereNull('race_date')->first();
                if ($seoul) {
                    $this->seedSmokeData($seoul, $upcoming);
                }
            }
            $this->info('Pilot seed (force) 완료.');

            return 0;
        }

        $this->info("races 베이스: {$racesCount}건");

        $created = [];

        foreach ($this->pilots as $pilot) {
            $race = Race::find($pilot['race_id']);
            if (! $race) {
                $this->error("race_id={$pilot['race_id']} ({$pilot['name']}) 없음 — skip");

                continue;
            }

            $edition = RaceEdition::create([
                'race_id'          => $race->id,
                'name'             => $race->name,
                'year'             => 2025,
                'race_date'        => Carbon::parse('2025-03-02')->addDays(match ($pilot['key']) {
                    'seoul' => 0, 'daegu' => 7, 'gyeongju' => 14, 'gunsan' => 21, default => 0,
                }),
                'location'         => $pilot['location'],
                'city'             => $pilot['city'],
                'is_domestic'      => true,
                'country'          => '대한민국',
                'weather_stn_id'   => $pilot['weather_stn_id'],
                'entry_fee'        => '70000',
                'status'           => 'ended',
                'is_review_open'   => true,
                'is_active'        => true,
                'source'           => 'pilot_seed',
            ]);

            RaceCourse::updateOrCreate(
                ['race_edition_id' => $edition->id, 'course_type' => 'FULL'],
                [
                    'gpx_url'      => "race-courses/{$edition->id}/FULL.gpx",
                    'source'       => 'official',
                    'is_certified' => true,
                ]
            );

            if (! $this->option('skip-weather')) {
                try {
                    $weatherService->getForEdition($edition);
                    $this->line("  [weather] edition #{$edition->id} ASOS 요청");
                } catch (\Throwable $e) {
                    $this->warn("  [weather] edition #{$edition->id} 실패: {$e->getMessage()}");
                }
            }

            $created[$pilot['key']] = $edition;
            $this->info("✓ {$race->name} → edition #{$edition->id} (ended, GPX)");
        }

        $upcomingRace = Race::whereNotIn('id', collect($this->pilots)->pluck('race_id'))
            ->where('is_active', true)
            ->where('name', 'ilike', '%마라톤%')
            ->orderBy('id')
            ->first();

        $upcomingEdition = null;
        if ($upcomingRace) {
            $upcomingEdition = RaceEdition::create([
                'race_id'        => $upcomingRace->id,
                'name'           => $upcomingRace->name,
                'year'           => 2026,
                'race_date'      => null,
                'location'       => $upcomingRace->city,
                'city'           => $upcomingRace->city,
                'is_domestic'    => true,
                'country'        => '대한민국',
                'status'         => 'upcoming',
                'is_review_open' => false,
                'is_active'      => true,
                'source'         => 'pilot_seed',
            ]);
            $this->info("✓ upcoming: {$upcomingRace->name} → edition #{$upcomingEdition->id}");
        }

        if ($this->option('smoke') && ! empty($created)) {
            $this->seedSmokeData($created['seoul'] ?? reset($created), $upcomingEdition);
        }

        $this->newLine();
        $this->table(
            ['key', 'race_id', 'edition_id', 'status'],
            collect($created)->map(fn ($ed, $key) => [
                $key, $ed->race_id, $ed->id, $ed->status,
            ])->values()->all()
        );

        $this->info('Pilot seed 완료. 검증: php artisan review:verify-remodel');

        return 0;
    }

    private function seedSmokeData(RaceEdition $endedEdition, ?RaceEdition $upcomingEdition): void
    {
        $user = User::query()->orderBy('id')->first();
        if (! $user) {
            $this->warn('  [smoke] users 없음 — review/feedback skip');

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

        $review = Review::where('user_id', $user->id)->where('race_edition_id', $endedEdition->id)->first();

        if (! $review) {
            $review = Review::create([
                'race_edition_id' => $endedEdition->id,
                'user_id'         => $user->id,
                'rating'          => 5,
                'content'         => 'Pilot smoke test — 코스·운영 모두 만족스러웠습니다.',
                'course_type'     => 'FULL',
                'finish_time'     => '03:45:00',
            ]);
            $this->line("  [smoke] review #{$review->id} on edition #{$endedEdition->id}");
        }

        app(WeatherCaseService::class)->upsertFromReview(
            $review->fresh(['raceEdition.race', 'raceEdition.weather'])
        );

        $race = $endedEdition->race;
        if ($race) {
            $summary = $race->ai_race_summary ?? [];
            $summary['_meta'] = array_merge($summary['_meta'] ?? [], ['dirty' => true]);
            $race->update(['ai_race_summary' => $summary]);
        }

        if ($upcomingEdition && ! EditionFeedback::where('user_id', $user->id)->where('race_edition_id', $upcomingEdition->id)->exists()) {
            EditionFeedback::create([
                'race_edition_id' => $upcomingEdition->id,
                'user_id'         => $user->id,
                'content'         => 'Pilot smoke — 코스 편성 기대합니다.',
                'category'        => 'course',
            ]);
            $this->line("  [smoke] feedback on upcoming edition #{$upcomingEdition->id}");
        }
    }
}

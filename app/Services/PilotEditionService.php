<?php

namespace App\Services;

use App\Models\Race;
use App\Models\RaceEdition;
use Illuminate\Support\Carbon;

class PilotEditionService
{
    /** @return array<string, array<string, mixed>> */
    public function pilots(): array
    {
        return config('pilot_races.pilots', []);
    }

    /** @return list<string> */
    public function pilotKeys(): array
    {
        return array_keys($this->pilots());
    }

    /**
     * Admin 대시보드 — pilot별 race + 보유 edition 연도.
     *
     * @return list<array<string, mixed>>
     */
    public function adminStatus(): array
    {
        $rows = [];

        foreach ($this->pilots() as $key => $pilot) {
            $race = Race::where('name', $pilot['name'])->first();
            $years = $race
                ? RaceEdition::where('race_id', $race->id)->orderBy('year')->pluck('year')->all()
                : [];

            $rows[] = [
                'key'           => $key,
                'name'          => $pilot['name'],
                'race_id'       => $race?->id,
                'edition_years' => $years,
            ];
        }

        return $rows;
    }

    /**
     * @param  list<int>  $years
     * @return list<array<string, mixed>>
     */
    public function preview(array $years): array
    {
        sort($years);
        $dateMap = $this->resolveDateMap($years);
        $rows = [];

        foreach ($this->pilots() as $key => $pilot) {
            $race = Race::where('name', $pilot['name'])->first();

            foreach ($years as $year) {
                $resolved = $dateMap[$key][$year] ?? ['race_date' => null, 'source' => 'null'];
                $raceDate = $resolved['race_date']
                    ? Carbon::parse($resolved['race_date'])
                    : null;
                [$status] = $this->deriveStatus($raceDate, $year);

                $exists = $race
                    ? RaceEdition::where('race_id', $race->id)->where('year', $year)->exists()
                    : false;

                $rows[] = [
                    'key'         => $key,
                    'name'        => $pilot['name'],
                    'year'        => $year,
                    'race_date'   => $resolved['race_date'],
                    'date_source' => $resolved['source'],
                    'status'      => $status,
                    'exists'      => $exists,
                    'race_id'     => $race?->id,
                ];
            }
        }

        return $rows;
    }

    /**
     * @param  list<int>  $years
     * @return array{created:int, updated:int, rows:list<array<string,mixed>>}
     */
    public function provision(array $years, ?int $gpxYear = null): array
    {
        sort($years);
        $gpxYear ??= (int) date('Y') - 1;
        $dateMap = $this->resolveDateMap($years);
        $created = $updated = 0;
        $resultRows = [];

        foreach ($this->pilots() as $key => $pilot) {
            $race = $this->upsertPilotRace($key);

            foreach ($years as $year) {
                $resolved = $dateMap[$key][$year] ?? ['race_date' => null, 'source' => 'null'];
                $raceDate = $resolved['race_date']
                    ? Carbon::parse($resolved['race_date'])
                    : null;
                [$status, $reviewOpen] = $this->deriveStatus($raceDate, $year);

                $existing = RaceEdition::where('race_id', $race->id)->where('year', $year)->first();
                $wasExisting = $existing !== null;

                $edition = RaceEdition::updateOrCreate(
                    [
                        'race_id' => $race->id,
                        'year'    => $year,
                    ],
                    [
                        'name'             => $race->name,
                        'race_date'        => $raceDate,
                        'location'         => $pilot['city'],
                        'city'             => $pilot['city'],
                        'is_domestic'      => true,
                        'country'          => '대한민국',
                        'weather_stn_id'   => $pilot['weather_stn_id'],
                        'entry_fee'        => $status === 'ended' ? '70000' : null,
                        'status'           => $status,
                        'is_review_open'   => $reviewOpen,
                        'is_active'        => true,
                        'source'           => 'pilot_provision',
                    ]
                );

                if ($year === $gpxYear && $status === 'ended') {
                    \App\Models\RaceCourse::updateOrCreate(
                        ['race_edition_id' => $edition->id, 'course_type' => 'FULL'],
                        [
                            'gpx_url'      => "race-courses/{$edition->id}/FULL.gpx",
                            'source'       => 'official',
                            'is_certified' => true,
                        ]
                    );
                }

                $wasExisting ? $updated++ : $created++;

                $resultRows[] = [
                    'key'         => $key,
                    'name'        => $pilot['name'],
                    'year'        => $year,
                    'edition_id'  => $edition->id,
                    'race_date'   => $raceDate?->format('Y-m-d'),
                    'date_source' => $resolved['source'],
                    'status'      => $status,
                    'action'      => $wasExisting ? 'updated' : 'created',
                ];
            }
        }

        return compact('created', 'updated', 'resultRows');
    }

    public function upsertPilotRace(string $key): Race
    {
        $pilot = $this->pilots()[$key] ?? null;
        if (! $pilot) {
            throw new \InvalidArgumentException("Unknown pilot key: {$key}");
        }

        $race = Race::firstOrCreate(
            ['name' => $pilot['name']],
            [
                'city'         => $pilot['city'],
                'is_domestic'  => true,
                'country'      => '대한민국',
                'is_active'    => true,
                'is_certified' => false,
                'wa_calendar'  => [],
            ]
        );

        if (! $race->wasRecentlyCreated) {
            $race->update([
                'city'        => $pilot['city'],
                'is_domestic' => true,
                'country'     => '대한민국',
            ]);
        }

        return $race;
    }

    /**
     * @param  list<int>  $years
     * @return array<string, array<int, array{race_date: ?string, source: string}>>
     */
    private function resolveDateMap(array $years): array
    {
        $map = [];
        foreach ($this->pilots() as $key => $pilot) {
            $map[$key] = [];
            foreach ($years as $year) {
                $dates = $pilot['dates'] ?? [];
                if (isset($dates[$year])) {
                    $map[$key][$year] = ['race_date' => $dates[$year], 'source' => 'catalog'];
                } else {
                    $map[$key][$year] = ['race_date' => null, 'source' => 'null'];
                }
            }
        }

        return $map;
    }

    /**
     * @return array{race_date: ?string, source: string}
     */
    public function resolveRaceDate(string $key, int $year): array
    {
        return $this->resolveDateMap([$year])[$key][$year]
            ?? ['race_date' => null, 'source' => 'null'];
    }

    /** @return array{0: string, 1: bool} */
    public function deriveStatus(?Carbon $raceDate, int $year): array
    {
        $today    = Carbon::today();
        $thisYear = (int) date('Y');

        if ($raceDate) {
            if ($raceDate->lt($today)) {
                return ['ended', true];
            }

            return ['upcoming', false];
        }

        if ($year < $thisYear) {
            return ['ended', false];
        }

        return ['upcoming', false];
    }
}

<?php

namespace App\Services;

use App\Models\Race;
use App\Models\RaceEdition;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PilotEditionService
{
    private string $coreApiUrl;

    public function __construct()
    {
        $this->coreApiUrl = rtrim(config('services.core_api.url', 'http://localhost:8100'), '/');
    }

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
            $race = $this->findPilotRace($key);
            $years = $race
                ? RaceEdition::where('race_id', $race->id)->orderBy('year')->pluck('year')->all()
                : [];

            $rows[] = [
                'key'           => $key,
                'name'          => $pilot['name'],
                'race_id'       => $race?->id,
                'race_name'     => $race?->name,
                'catalog_race_id' => $this->findCatalogRace($key, $pilot)?->id,
                'edition_years' => $years,
            ];
        }

        return $rows;
    }

    /**
     * @param  list<int>  $years
     * @return list<array<string, mixed>>
     */
    public function preview(array $years, bool $fetchExternal = true): array
    {
        sort($years);
        $dateMap = $this->resolveDateMap($years, $fetchExternal);
        $rows = [];

        foreach ($this->pilots() as $key => $pilot) {
            $race = $this->findPilotRace($key);

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
    public function provision(array $years, bool $fetchExternal = true): array
    {
        sort($years);
        $dateMap = $this->resolveDateMap($years, $fetchExternal);
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

            // 카탈로그 races에 edition이 붙으면 공개 승격 (신규 race 중복 생성 없이)
            $this->publishRace($race);
        }

        return compact('created', 'updated', 'resultRows');
    }

    public function upsertPilotRace(string $key): Race
    {
        $pilot = $this->pilots()[$key] ?? null;
        if (! $pilot) {
            throw new \InvalidArgumentException("Unknown pilot key: {$key}");
        }

        if ($catalog = $this->findCatalogRace($key, $pilot)) {
            $this->applyPilotRaceDefaults($catalog, $pilot);

            return $catalog->fresh();
        }

        // WA 카탈로그에 없을 때만 pilot 전용 races 행 생성 (폴백)
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

        $this->applyPilotRaceDefaults($race, $pilot);

        return $race->fresh();
    }

    /**
     * pilot provision 대상 races — WA sync 카탈로그 우선, 없으면 legacy pilot name.
     */
    public function findPilotRace(string $key): ?Race
    {
        $pilot = $this->pilots()[$key] ?? null;
        if (! $pilot) {
            return null;
        }

        return $this->findCatalogRace($key, $pilot)
            ?? Race::where('name', $pilot['name'])->first();
    }

    /**
     * WA Label sync 등으로 이미 races에 있는 국내 4대회 카탈로그 행을 찾는다.
     * search_names·도시·wa_label 기준. (pilot name exact match는 제외 — 별도 레코드 생성 방지)
     */
    private function findCatalogRace(string $key, array $pilot): ?Race
    {
        if (! empty($pilot['catalog_race_id'])) {
            return Race::find($pilot['catalog_race_id']);
        }

        // config 정식 명칭 exact / ilike (신규 race 중복 생성 방지)
        $byOfficial = Race::query()
            ->where('country', '대한민국')
            ->where(function ($q) use ($pilot) {
                $q->where('name', $pilot['name'])
                    ->orWhere('name', 'ilike', $pilot['name']);
            })
            ->orderByRaw('CASE WHEN wa_label IS NOT NULL THEN 0 ELSE 1 END')
            ->first();
        if ($byOfficial) {
            return $byOfficial;
        }

        $names = array_values(array_unique(array_filter($pilot['search_names'] ?? [])));

        foreach ($names as $name) {
            $race = Race::query()
                ->where('name', 'ilike', $name)
                ->where('country', '대한민국')
                ->orderByRaw('CASE WHEN wa_label IS NOT NULL THEN 0 ELSE 1 END')
                ->first();
            if ($race) {
                return $race;
            }
        }

        $city = $pilot['city'];

        return Race::query()
            ->where('country', '대한민국')
            ->whereNotNull('wa_label')
            ->where(function ($q) use ($city) {
                $q->where('name', 'ilike', '%'.$city.'%')
                    ->orWhere('name', 'ilike', '%'.mb_strtolower($city).'%');
            })
            ->orderByRaw("CASE wa_label WHEN 'platinum' THEN 1 WHEN 'gold' THEN 2 WHEN 'elite' THEN 3 ELSE 4 END")
            ->first();
    }

    public function catalogRace(string $key): ?Race
    {
        $pilot = $this->pilots()[$key] ?? null;

        return $pilot ? $this->findCatalogRace($key, $pilot) : null;
    }

    /** pilot config 이름으로만 만든 orphan races (#283~286 등). 카탈로그와 다를 때만 반환. */
    public function legacyOrphanRace(string $key): ?Race
    {
        $pilot = $this->pilots()[$key] ?? null;
        if (! $pilot) {
            return null;
        }

        $legacy  = Race::where('name', $pilot['name'])->first();
        $catalog = $this->findCatalogRace($key, $pilot);

        if (! $legacy || ($catalog && $legacy->id === $catalog->id)) {
            return null;
        }

        return $legacy;
    }

    public function applyPilotRaceDefaults(Race $race, array $pilot): void
    {
        $race->update([
            'city'        => $pilot['city'],
            'is_domestic' => true,
            'country'     => '대한민국',
            'is_active'   => true,
        ]);
    }

    /** provision 후 해당 마스터를 공개 대상으로 승격 */
    private function publishRace(Race $race): void
    {
        app(RaceService::class)->publishIfHasEditions($race->fresh());
    }

    /**
     * 종료된 pilot edition에 GPX 코스 스텁 등록 (edition 생성과 별도).
     *
     * @return list<array<string, mixed>>
     */
    public function attachGpxStub(int $year): array
    {
        $rows = [];

        foreach ($this->pilots() as $key => $pilot) {
            $race = $this->findPilotRace($key);
            if (! $race) {
                $rows[] = [
                    'name'   => $pilot['name'],
                    'year'   => $year,
                    'action' => 'skipped',
                    'reason' => 'race 없음',
                ];
                continue;
            }

            $edition = RaceEdition::where('race_id', $race->id)->where('year', $year)->first();
            if (! $edition) {
                $rows[] = [
                    'name'   => $pilot['name'],
                    'year'   => $year,
                    'action' => 'skipped',
                    'reason' => 'edition 없음',
                ];
                continue;
            }

            [$status] = $this->deriveStatus($edition->race_date, $year);
            if ($status !== 'ended') {
                $rows[] = [
                    'name'        => $pilot['name'],
                    'year'        => $year,
                    'edition_id'  => $edition->id,
                    'action'      => 'skipped',
                    'reason'      => '종료(ended) 아님 — status: '.$status,
                ];
                continue;
            }

            \App\Models\RaceCourse::updateOrCreate(
                ['race_edition_id' => $edition->id, 'course_type' => 'FULL'],
                [
                    'gpx_url'      => "race-courses/{$edition->id}/FULL.gpx",
                    'source'       => 'official',
                    'is_certified' => true,
                ]
            );

            $rows[] = [
                'name'       => $pilot['name'],
                'year'       => $year,
                'edition_id' => $edition->id,
                'action'     => 'attached',
                'reason'     => null,
            ];
        }

        return $rows;
    }

    /**
     * Admin — catalog에 등록된 날짜 연도 (자동 조회 가능 범위).
     *
     * @return list<array{name: string, catalog_years: list<int>}>
     */
    public function catalogCoverage(): array
    {
        $rows = [];
        foreach ($this->pilots() as $pilot) {
            $years = array_map('intval', array_keys($pilot['dates'] ?? []));
            sort($years);
            $rows[] = [
                'name'          => $pilot['name'],
                'catalog_years' => $years,
            ];
        }

        return $rows;
    }

    /**
     * @param  list<int>  $years
     * @return array<string, array<int, array{race_date: ?string, source: string}>>
     */
    private function resolveDateMap(array $years, bool $fetchExternal): array
    {
        $map = [];
        foreach ($this->pilots() as $key => $pilot) {
            $map[$key] = [];
            foreach ($years as $year) {
                $dates = $pilot['dates'] ?? [];
                $date = $dates[$year] ?? $dates[(string) $year] ?? null;
                if ($date !== null) {
                    $map[$key][$year] = ['race_date' => $date, 'source' => 'catalog'];
                } else {
                    $map[$key][$year] = ['race_date' => null, 'source' => 'null'];
                }
            }
        }

        if (! $fetchExternal) {
            return $map;
        }

        $lookupYears = array_values(array_filter($years, function (int $y) use ($map) {
            if ($y > (int) date('Y') + 1) {
                return false;
            }
            foreach ($map as $byYear) {
                if (($byYear[$y]['source'] ?? '') === 'null') {
                    return true;
                }
            }

            return false;
        }));

        if ($lookupYears === []) {
            return $map;
        }

        try {
            $response = Http::timeout(180)
                ->acceptJson()
                ->get("{$this->coreApiUrl}/api/races/pilot-edition-dates", [
                    'years'          => implode(',', $lookupYears),
                    'fetch_external' => 'true',
                ]);

            if ($response->successful()) {
                foreach ($response->json() as $row) {
                    $k = $row['key'] ?? null;
                    $y = (int) ($row['year'] ?? 0);
                    if ($k && isset($map[$k][$y]) && ($map[$k][$y]['source'] === 'null')) {
                        $map[$k][$y] = [
                            'race_date' => $row['race_date'] ?? null,
                            'source'    => $row['source'] ?? 'null',
                        ];
                    }
                }
            } else {
                Log::warning('Pilot batch date lookup HTTP error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Pilot batch date lookup failed', ['error' => $e->getMessage()]);
        }

        return $map;
    }

    /**
     * @return array{race_date: ?string, source: string}
     */
    public function resolveRaceDate(string $key, int $year, bool $fetchExternal = true): array
    {
        return $this->resolveDateMap([$year], $fetchExternal)[$key][$year]
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

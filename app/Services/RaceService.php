<?php

namespace App\Services;

use App\Models\Race;
use App\Models\RaceEdition;
use Illuminate\Pagination\LengthAwarePaginator;

class RaceService
{
    // races 테이블 전용 필드
    private const RACE_FIELDS = [
        'name', 'name_en', 'city', 'organizer',
        'website_url', 'official_url', 'is_active',
        'wa_label', 'is_certified', 'is_domestic', 'country',
    ];

    // race_editions 테이블 전용 필드
    private const EDITION_FIELDS = [
        'race_date', 'race_time', 'location', 'entry_fee',
        'reg_start', 'reg_end', 'status', 'weather_stn_id',
    ];

    // ─── 공개용 ───────────────────────────────────────────────

    public function getPublicList(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        return Race::active()
            ->byCity($filters['city'] ?? null)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /** 대회 목록 — 다가오는 대회 / 지난 대회 리뷰 2단 구조 + 연도 집계 (TASK-1) */
    public function getSectionedList(array $filters): array
    {
        return Race::sectionedList($filters);
    }

    /** 히어로 통계 카드용 — 전체 대회/누적 리뷰/리뷰 있는 대회 수 (필터 무관 전역 집계) */
    public function getGlobalStats(): array
    {
        return Race::globalStats();
    }

    // ─── 관리자용 ─────────────────────────────────────────────

    public function getAdminList(int $perPage = 20): LengthAwarePaginator
    {
        return Race::with('latestEdition')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * 대회 등록 — races 마스터 생성 + race_editions 최초 인스턴스 생성
     */
    public function create(array $validated, string $distancesRaw = ''): Race
    {
        $raceData             = array_intersect_key($validated, array_flip(self::RACE_FIELDS));
        $raceData['distances'] = $this->parseDistances($distancesRaw);

        $race = Race::create($raceData);

        $editionData = array_intersect_key($validated, array_flip(self::EDITION_FIELDS));
        $year = isset($editionData['race_date']) && $editionData['race_date']
            ? \Carbon\Carbon::parse($editionData['race_date'])->year
            : 0;

        $edition = RaceEdition::create(array_merge($editionData, [
            'race_id'     => $race->id,
            'name'        => $race->name,
            'year'        => $year,
            'city'        => $race->city,
            'is_domestic' => $race->is_domestic ?? true,
            'country'     => $race->country ?? '대한민국',
        ]));

        if (!$edition->weather_stn_id && ($edition->location || $edition->city)) {
            app(WeatherService::class)->autoResolveStnForEdition($edition);
        }

        return $race->fresh();
    }

    /**
     * 대회 수정 — races 마스터 수정 + 최신 race_edition 동기화
     */
    public function update(Race $race, array $validated, string $distancesRaw = ''): Race
    {
        $raceData             = array_intersect_key($validated, array_flip(self::RACE_FIELDS));
        $raceData['distances'] = $this->parseDistances($distancesRaw);

        $race->update($raceData);
        $race->refresh();

        $editionData = array_intersect_key($validated, array_flip(self::EDITION_FIELDS));
        $edition     = RaceEdition::where('race_id', $race->id)->orderByDesc('year')->first();

        if ($edition) {
            $locationChanged =
                (isset($editionData['location']) && $editionData['location'] !== $edition->location) ||
                ($race->city !== $edition->city);

            if ($locationChanged && empty($editionData['weather_stn_id'])) {
                $editionData['weather_stn_id'] = null;
            }

            $edition->update(array_merge($editionData, [
                'name' => $race->name,
                'city' => $race->city,
            ]));
            $edition->refresh();

            if ($locationChanged && !$edition->weather_stn_id) {
                app(WeatherService::class)->autoResolveStnForEdition($edition);
            }
        } else {
            $year = isset($editionData['race_date']) && $editionData['race_date']
                ? \Carbon\Carbon::parse($editionData['race_date'])->year
                : 0;
            RaceEdition::create(array_merge($editionData, [
                'race_id'     => $race->id,
                'name'        => $race->name,
                'year'        => $year,
                'city'        => $race->city,
                'is_domestic' => $race->is_domestic ?? true,
                'country'     => $race->country ?? '대한민국',
            ]));
        }

        return $race->fresh();
    }

    public function delete(Race $race): void
    {
        $race->delete();
    }

    // ─── Private ──────────────────────────────────────────────

    private function parseDistances(string $raw): ?array
    {
        if (trim($raw) === '') {
            return null;
        }
        return array_values(
            array_filter(
                array_map('trim', explode(',', $raw))
            )
        );
    }
}

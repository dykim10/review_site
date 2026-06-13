<?php

namespace App\Services;

use App\Models\Race;
use App\Models\RaceEdition;
use Illuminate\Pagination\LengthAwarePaginator;

class RaceEditionService
{
    public function getAdminList(int $perPage = 20): LengthAwarePaginator
    {
        return RaceEdition::with('race')
            ->orderByDesc('race_date')
            ->paginate($perPage);
    }

    public function getPublicListWithStats(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return RaceEdition::listWithReviewStats($filters, $perPage, request()->integer('page', 1));
    }

    /**
     * races 마스터에서 race_editions 인스턴스 생성.
     * 대회 등록 시 자동 호출.
     */
    public function createFromRace(Race $race): RaceEdition
    {
        $year = $race->race_date?->year ?? 0;

        $edition = RaceEdition::create([
            'race_id'                    => $race->id,
            'name'                       => $race->name,
            'year'                       => $year,
            'race_date'                  => $race->race_date,
            'race_time'                  => $race->race_time,
            'location'                   => $race->location,
            'city'                       => $race->city,
            'is_domestic'                => $race->is_domestic ?? true,
            'country'                    => $race->country ?? '대한민국',
            'source'                     => $race->source,
            'source_url'                 => $race->source_url,
            'weather_stn_id'             => $race->weather_stn_id,
            'weather_fetch_attempted_at' => $race->weather_fetch_attempted_at,
            'entry_fee'                  => $race->entry_fee,
            'reg_start'                  => $race->reg_start,
            'reg_end'                    => $race->reg_end,
            'status'                     => $race->status,
            'is_active'                  => $race->is_active,
        ]);

        return $edition;
    }

    /**
     * race 수정 시 연결된 edition도 동기화.
     */
    public function syncFromRace(Race $race): void
    {
        $edition = RaceEdition::where('race_id', $race->id)->first();
        if (!$edition) return;

        $edition->update([
            'name'                       => $race->name,
            'year'                       => $race->race_date?->year ?? $edition->year,
            'race_date'                  => $race->race_date,
            'race_time'                  => $race->race_time,
            'location'                   => $race->location,
            'city'                       => $race->city,
            'is_domestic'                => $race->is_domestic ?? $edition->is_domestic,
            'country'                    => $race->country ?? $edition->country,
            'source'                     => $race->source,
            'source_url'                 => $race->source_url,
            'weather_stn_id'             => $race->weather_stn_id,
            'weather_fetch_attempted_at' => $race->weather_fetch_attempted_at,
            'entry_fee'                  => $race->entry_fee,
            'reg_start'                  => $race->reg_start,
            'reg_end'                    => $race->reg_end,
            'status'                     => $race->status,
            'is_active'                  => $race->is_active,
        ]);
    }

    public function create(array $validated): RaceEdition
    {
        return RaceEdition::create($validated);
    }

    public function update(RaceEdition $edition, array $validated): RaceEdition
    {
        $edition->update($validated);
        return $edition->fresh();
    }

    public function delete(RaceEdition $edition): void
    {
        $edition->delete();
    }
}

<?php

namespace App\Services;

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

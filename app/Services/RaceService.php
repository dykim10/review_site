<?php

namespace App\Services;

use App\Models\Race;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\RaceEditionService;

class RaceService
{
    // ─── 공개용 ───────────────────────────────────────────────

    /**
     * 공개 대회 목록 (필터 적용, 페이지네이션)
     */
    public function getPublicList(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        return Race::active()
            ->upcoming()
            ->byCity($filters['city'] ?? null)
            ->byStatus($filters['status'] ?? null)
            ->orderBy('race_date')
            ->paginate($perPage);
    }

    /**
     * 공개 대회 목록 + 리뷰 통계 (avg_rating, review_count 포함, 페이지네이션)
     */
    public function getPublicListWithStats(array $filters, int $perPage = 20): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Race::listWithReviewStats($filters, $perPage, request()->integer('page', 1));
    }

    // ─── 관리자용 ─────────────────────────────────────────────

    /**
     * 관리자 대회 목록 (전체, 최신순)
     */
    public function getAdminList(int $perPage = 20): LengthAwarePaginator
    {
        return Race::orderByDesc('race_date')->paginate($perPage);
    }

    /**
     * 대회 등록 — race + race_edition 동시 생성
     */
    public function create(array $validated, string $distancesRaw = ''): Race
    {
        $validated['distances'] = $this->parseDistances($distancesRaw);
        $race = Race::create($validated);

        app(WeatherService::class)->autoResolveStn($race);
        $race->refresh();

        app(RaceEditionService::class)->createFromRace($race);

        return $race->fresh();
    }

    /**
     * 대회 수정 — race + 연결된 race_edition 동기화
     */
    public function update(Race $race, array $validated, string $distancesRaw = ''): Race
    {
        $validated['distances'] = $this->parseDistances($distancesRaw);

        $locationChanged = isset($validated['location']) && $validated['location'] !== $race->location
            || isset($validated['city']) && $validated['city'] !== $race->city;

        if ($locationChanged && !$race->weather_stn_id) {
            $validated['weather_stn_id'] = null;
        }

        $race->update($validated);
        $race->refresh();

        if ($locationChanged) {
            app(WeatherService::class)->autoResolveStn($race);
            $race->refresh();
        }

        app(RaceEditionService::class)->syncFromRace($race);

        return $race->fresh();
    }

    /**
     * 대회 삭제
     */
    public function delete(Race $race): void
    {
        $race->delete();
    }

    // ─── Private 정제 메서드 ──────────────────────────────────

    /**
     * "5K,10K,하프,풀" 형태의 문자열을 배열로 변환
     */
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

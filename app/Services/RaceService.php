<?php

namespace App\Services;

use App\Models\Race;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * 대회 등록
     */
    public function create(array $validated, string $distancesRaw = ''): Race
    {
        $validated['distances'] = $this->parseDistances($distancesRaw);
        return Race::create($validated);
    }

    /**
     * 대회 수정
     */
    public function update(Race $race, array $validated, string $distancesRaw = ''): Race
    {
        $validated['distances'] = $this->parseDistances($distancesRaw);
        $race->update($validated);
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

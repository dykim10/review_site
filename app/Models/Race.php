<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Race extends Model
{
    protected $table = 'review.races';

    protected $fillable = [
        'name', 'name_en', 'city', 'organizer', 'distances',
        'website_url', 'official_url', 'is_active', 'is_published',
        'ai_race_summary', 'wa_label', 'is_certified',
        'is_domestic', 'country', 'wa_calendar',
    ];

    protected function casts(): array
    {
        return [
            'is_active'       => 'boolean',
            'is_published'    => 'boolean',
            'is_certified'    => 'boolean',
            'is_domestic'     => 'boolean',
            'wa_calendar'     => 'array',
            'ai_race_summary' => 'array',
        ];
    }

    // ─── Accessors ────────────────────────────────────────────

    protected function distances(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_array($value)) return $value;
                if (is_null($value) || $value === '') return null;

                $decoded = json_decode($value, true);
                if (is_array($decoded)) return $decoded;

                if (str_starts_with($value, '{') && str_ends_with($value, '}')) {
                    $inner = substr($value, 1, -1);
                    return $inner !== '' ? str_getcsv($inner, ',', '"') : [];
                }

                return null;
            },
            set: fn ($value) => is_array($value)
                ? json_encode($value, JSON_UNESCAPED_UNICODE)
                : $value,
        );
    }

    // ─── Relationships ────────────────────────────────────────

    public function editions()
    {
        return $this->hasMany(RaceEdition::class);
    }

    /** 최신 연도(year max) 기준 단일 edition */
    public function latestEdition()
    {
        return $this->hasOne(RaceEdition::class)->ofMany('year', 'max');
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByCity($query, ?string $city)
    {
        return $city ? $query->where('city', $city) : $query;
    }

    public function scopeCertified($query)
    {
        return $query->where('is_certified', true);
    }

    public function scopeByLabel($query, ?string $label)
    {
        return $label ? $query->where('wa_label', $label) : $query;
    }

    // ─── Static Methods ───────────────────────────────────────

    /**
     * 전체 대회 수 / 누적 리뷰 수 / 리뷰 있는 대회 수 (필터 없는 전역 통계).
     * 목록 히어로 통계 카드용 — 페이지네이션된 목록과 별개로 집계.
     */
    public static function globalStats(): array
    {
        $row = DB::selectOne(
            "SELECT
                COUNT(DISTINCT r.id) AS total_races,
                COUNT(rv.id) AS total_reviews,
                COUNT(DISTINCT CASE WHEN rv.id IS NOT NULL THEN r.id END) AS races_with_reviews
             FROM review.races r
             LEFT JOIN review.race_editions ed ON ed.race_id = r.id
             LEFT JOIN review.reviews rv ON rv.race_edition_id = ed.id
             WHERE r.is_active = true AND r.is_published = true"
        );

        return [
            'total_races'        => (int) $row->total_races,
            'total_reviews'      => (int) $row->total_reviews,
            'races_with_reviews' => (int) $row->races_with_reviews,
        ];
    }

    /**
     * 대회 목록 — 다가오는 대회 / 지난 대회 리뷰 / 공인 카탈로그(참고용) 3단 구조 (TASK-1).
     * 행 단위 = edition (race가 아님) — edition.status='upcoming' → 다가오는 대회(기대/개선 의견),
     * status='ended' → 지난 대회 리뷰( race당 카드 1개 + 개최년도 칩 ). 같은 race의 여러 ended edition은
     * past 그룹 안 editions[] 로 묶음. edition 자체가 없는 race(WA sync 카탈로그 등) → 공인 카탈로그.
     * 연도 비교(latest_edition_year >= 올해)가 아니라 edition.status를 그대로 신뢰 — race_date가 같은 해
     * 안에서도 이미 지난 대회(예: 2026-03 서울)와 아직 안 지난 대회(예: 2026-10 경주)가 섞이기 때문.
     * '접수 상태' 필터는 폐기, 국내외/공인등급/연도/리뷰유무로 대체.
     */
    public static function sectionedList(array $filters = []): array
    {
        $isDomestic = $filters['is_domestic'] ?? null; // '1' | '0' | null
        $waLabel    = $filters['wa_label']    ?? null;  // platinum|gold|elite|label
        $year       = $filters['year']        ?? null;
        $hasReview  = $filters['has_review']  ?? null;

        $baseWhere = "WHERE r.is_active = true AND r.is_published = true";
        $bindings  = [];

        if ($isDomestic !== null && $isDomestic !== '') {
            $baseWhere .= " AND r.is_domestic = :is_domestic";
            $bindings['is_domestic'] = (bool) $isDomestic;
        }
        if ($waLabel) {
            $baseWhere .= " AND r.wa_label = :wa_label";
            $bindings['wa_label'] = $waLabel;
        }

        $editionWhere    = $baseWhere;
        $editionBindings = $bindings;
        if ($year) {
            $editionWhere .= " AND ed.year = :year";
            $editionBindings['year'] = $year;
        }

        $having = $hasReview ? "HAVING COUNT(rv.id) > 0" : "";

        // edition 단위 행 — 같은 race가 여러 edition을 가지면 연도마다 한 행
        $editionRows = collect(DB::select(
            "SELECT r.id AS race_id, r.name, r.wa_label, r.city, r.distances, r.ai_race_summary,
                    ed.id AS edition_id, ed.year, ed.race_date, ed.status, ed.location, ed.entry_fee,
                    COUNT(rv.id) AS review_count,
                    ROUND(AVG(rv.rating)::numeric, 1) AS avg_rating
             FROM review.races r
             JOIN review.race_editions ed ON ed.race_id = r.id
             LEFT JOIN review.reviews rv ON rv.race_edition_id = ed.id
             $editionWhere
             GROUP BY r.id, ed.id, ed.year, ed.race_date, ed.status, ed.location, ed.entry_fee
             $having",
            $editionBindings
        ));

        $upcoming = $editionRows
            ->filter(fn ($r) => $r->status === 'upcoming')
            ->sortBy(fn ($r) => $r->race_date ?? '9999-12-31')
            ->values();

        $pastFlat = $editionRows
            ->filter(fn ($r) => $r->status === 'ended')
            ->values();

        $past = $pastFlat
            ->groupBy('race_id')
            ->map(function ($rows, $raceId) {
                $first = $rows->first();
                $editions = $rows
                    ->sortByDesc(fn ($r) => $r->year ?? 0)
                    ->values()
                    ->map(fn ($r) => (object) [
                        'edition_id'   => (int) $r->edition_id,
                        'year'         => $r->year,
                        'race_date'    => $r->race_date,
                        'review_count' => (int) $r->review_count,
                        'avg_rating'   => (float) ($r->avg_rating ?? 0),
                    ]);

                $totalReviews = $editions->sum('review_count');
                $avgRating    = null;
                if ($totalReviews > 0) {
                    $weighted = $editions
                        ->filter(fn ($e) => $e->review_count > 0)
                        ->reduce(fn ($sum, $e) => $sum + ($e->avg_rating * $e->review_count), 0.0);
                    $avgRating = round($weighted / $totalReviews, 1);
                }

                $linkEdition = $editions->first(fn ($e) => $e->review_count > 0) ?? $editions->first();
                $latest      = $editions->first();
                $currentYear = (int) date('Y');
                $currentYearEdition = $editions->first(fn ($e) => (int) ($e->year ?? 0) === $currentYear)
                    ?? $latest;

                return (object) [
                    'race_id'                    => (int) $raceId,
                    'name'                       => $first->name,
                    'city'                       => $first->city,
                    'wa_label'                   => $first->wa_label,
                    'distances'                  => $first->distances,
                    'ai_race_summary'            => $first->ai_race_summary,
                    'editions'                   => $editions,
                    'total_review_count'         => $totalReviews,
                    'avg_rating'                 => $avgRating,
                    'link_edition_id'            => $linkEdition->edition_id,
                    'current_year_edition_id'    => $currentYearEdition->edition_id,
                    'sort_date'                  => $latest->race_date ?? (($latest->year ?? 0).'-01-01'),
                ];
            })
            ->sortByDesc('sort_date')
            ->values();

        // edition이 아예 없는 race — WA sync 카탈로그 등 (리뷰 작성 불가, 참고용)
        // year/has_review 필터는 edition 존재를 전제하므로, 걸려 있으면 카탈로그 섹션은 비움
        $catalogOnly = collect();
        if (! $year && ! $hasReview) {
            $catalogOnly = collect(DB::select(
                "SELECT r.* FROM review.races r
                 $baseWhere
                 AND NOT EXISTS (SELECT 1 FROM review.race_editions ed2 WHERE ed2.race_id = r.id)
                 ORDER BY r.name",
                $bindings
            ));
        }

        $years = collect(DB::select(
            "SELECT ed.year, COUNT(*) AS cnt
             FROM review.race_editions ed
             JOIN review.races r ON r.id = ed.race_id
             WHERE r.is_active = true AND r.is_published = true AND ed.year IS NOT NULL
             GROUP BY ed.year
             ORDER BY ed.year DESC"
        ));

        return ['upcoming' => $upcoming, 'past' => $past, 'catalogOnly' => $catalogOnly, 'years' => $years];
    }
}

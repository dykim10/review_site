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
        'website_url', 'official_url', 'is_active',
        'ai_race_summary', 'wa_label', 'is_certified',
        'is_domestic', 'country', 'wa_calendar',
    ];

    protected function casts(): array
    {
        return [
            'is_active'       => 'boolean',
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
             WHERE r.is_active = true"
        );

        return [
            'total_races'        => (int) $row->total_races,
            'total_reviews'      => (int) $row->total_reviews,
            'races_with_reviews' => (int) $row->races_with_reviews,
        ];
    }

    /**
     * 대회 목록 — 다가오는 대회 / 지난 대회 리뷰 2단 구조 (TASK-1).
     * 최신 에디션 연도 >= 올해 → 다가오는 대회, 그 외(과거 연도·에디션 없음) → 지난 대회.
     * '접수 상태' 필터는 폐기, 국내외/공인등급/연도/리뷰유무로 대체.
     */
    public static function sectionedList(array $filters = []): array
    {
        $isDomestic = $filters['is_domestic'] ?? null; // '1' | '0' | null
        $waLabel    = $filters['wa_label']    ?? null;  // platinum|gold|elite|label
        $year       = $filters['year']        ?? null;
        $hasReview  = $filters['has_review']  ?? null;

        $where    = "WHERE r.is_active = true";
        $bindings = [];

        if ($isDomestic !== null && $isDomestic !== '') {
            $where .= " AND r.is_domestic = :is_domestic";
            $bindings['is_domestic'] = (bool) $isDomestic;
        }
        if ($waLabel) {
            $where .= " AND r.wa_label = :wa_label";
            $bindings['wa_label'] = $waLabel;
        }
        if ($year) {
            $where .= " AND ed.year = :year";
            $bindings['year'] = $year;
        }

        $having = $hasReview ? "HAVING COUNT(rv.id) > 0" : "";

        $rows = DB::select(
            "SELECT r.*,
                    ed.id AS latest_edition_id,
                    ed.year AS latest_edition_year,
                    ed.race_date, ed.status, ed.location, ed.entry_fee,
                    COUNT(rv.id) AS review_count,
                    ROUND(AVG(rv.rating)::numeric, 1) AS avg_rating
             FROM review.races r
             LEFT JOIN LATERAL (
                 SELECT id, year, race_date, status, location, entry_fee
                 FROM review.race_editions
                 WHERE race_id = r.id
                 ORDER BY year DESC NULLS LAST
                 LIMIT 1
             ) ed ON true
             LEFT JOIN review.reviews rv ON rv.race_edition_id = ed.id
             $where
             GROUP BY r.id, ed.id, ed.year, ed.race_date, ed.status, ed.location, ed.entry_fee
             $having",
            $bindings
        );

        $currentYear = now()->year;
        $rows        = collect($rows);

        $upcoming = $rows
            ->filter(fn ($r) => $r->latest_edition_year !== null && $r->latest_edition_year >= $currentYear)
            ->sortBy([
                fn ($r) => $r->latest_edition_id === null ? 1 : 0,
                'latest_edition_year',
            ])
            ->values();

        $past = $rows
            ->filter(fn ($r) => $r->latest_edition_year === null || $r->latest_edition_year < $currentYear)
            ->sort(function ($a, $b) {
                $hasEditionA = $a->latest_edition_id !== null ? 0 : 1;
                $hasEditionB = $b->latest_edition_id !== null ? 0 : 1;
                if ($hasEditionA !== $hasEditionB) {
                    return $hasEditionA <=> $hasEditionB;
                }
                $yearCmp = ($b->latest_edition_year ?? 0) <=> ($a->latest_edition_year ?? 0);
                return $yearCmp !== 0 ? $yearCmp : ($b->review_count <=> $a->review_count);
            })
            ->values();

        $years = collect(DB::select(
            "SELECT ed.year, COUNT(*) AS cnt
             FROM review.race_editions ed
             JOIN review.races r ON r.id = ed.race_id
             WHERE r.is_active = true AND ed.year IS NOT NULL
             GROUP BY ed.year
             ORDER BY ed.year DESC"
        ));

        return ['upcoming' => $upcoming, 'past' => $past, 'years' => $years];
    }
}

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
        'is_domestic', 'country',
    ];

    protected function casts(): array
    {
        return [
            'is_active'       => 'boolean',
            'is_certified'    => 'boolean',
            'is_domestic'     => 'boolean',
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
     * 대회 목록 + 리뷰 수 / 평균 평점 JOIN.
     * race_editions LATERAL JOIN으로 최신 edition의 날짜·상태·장소·참가비를 함께 반환.
     */
    public static function listWithReviewStats(
        array $filters = [],
        int $perPage = 20,
        int $page = 1,
    ): \Illuminate\Pagination\LengthAwarePaginator {
        $city    = $filters['city']     ?? null;
        $status  = $filters['status']   ?? null;
        $waLabel = $filters['wa_label'] ?? null;

        $where = "WHERE r.is_active = true"
            . ($city    ? " AND r.city = :city"         : '')
            . ($status  ? " AND ed.status = :status"    : '')
            . ($waLabel ? " AND r.wa_label = :wa_label" : '');

        $bindings = [];
        if ($city)    $bindings['city']     = $city;
        if ($status)  $bindings['status']   = $status;
        if ($waLabel) $bindings['wa_label'] = $waLabel;

        $total = DB::selectOne(
            "SELECT COUNT(*) AS cnt
             FROM review.races r
             LEFT JOIN LATERAL (
                 SELECT race_date, status, location, entry_fee
                 FROM review.race_editions
                 WHERE race_id = r.id
                 ORDER BY year DESC NULLS LAST
                 LIMIT 1
             ) ed ON true
             $where",
            $bindings
        )->cnt;

        $offset = ($page - 1) * $perPage;
        $rows   = DB::select(
            "SELECT r.*,
                    COUNT(rv.id) AS review_count,
                    ROUND(AVG(rv.rating)::numeric, 1) AS avg_rating,
                    ed.race_date, ed.status, ed.location, ed.entry_fee
             FROM review.races r
             LEFT JOIN review.reviews rv ON rv.race_id = r.id
             LEFT JOIN LATERAL (
                 SELECT race_date, status, location, entry_fee
                 FROM review.race_editions
                 WHERE race_id = r.id
                 ORDER BY year DESC NULLS LAST
                 LIMIT 1
             ) ed ON true
             $where
             GROUP BY r.id, ed.race_date, ed.status, ed.location, ed.entry_fee
             ORDER BY ed.race_date DESC NULLS LAST
             LIMIT :limit OFFSET :offset",
            array_merge($bindings, ['limit' => $perPage, 'offset' => $offset])
        );

        return new \Illuminate\Pagination\LengthAwarePaginator(
            collect($rows),
            (int) $total,
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
    }
}

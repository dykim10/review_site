<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Race extends Model
{
    protected $table = 'review.races';

    protected $fillable = [
        'name', 'race_date', 'race_time', 'location', 'city',
        'organizer', 'distances', 'entry_fee', 'website_url',
        'reg_start', 'reg_end', 'status', 'source', 'source_url', 'is_active',
        'ai_race_summary',
    ];

    protected function casts(): array
    {
        return [
            'is_active'       => 'boolean',
            'race_date'       => 'date',
            'reg_start'       => 'date',
            'reg_end'         => 'date',
            'ai_race_summary' => 'array',
        ];
    }

    // ─── Accessors / Mutators ─────────────────────────────────

    protected function distances(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_array($value)) return $value;
                if (is_null($value) || $value === '') return null;

                // JSON format: ["풀","하프","10K","5K"]
                $decoded = json_decode($value, true);
                if (is_array($decoded)) return $decoded;

                // PostgreSQL text[] format: {"풀","하프","10K","5K"}
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

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('race_date', '>=', now()->toDateString());
    }

    public function scopeByCity($query, ?string $city)
    {
        return $city ? $query->where('city', $city) : $query;
    }

    public function scopeByStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    // ─── Static Methods (복잡한 쿼리) ─────────────────────────

    /**
     * 대회 목록 + 리뷰 수 / 평균 평점 JOIN (raw query, 페이지네이션)
     * 사용: 공개 대회 목록에서 리뷰 통계 함께 표시할 때
     */
    public static function listWithReviewStats(
        array $filters = [],
        int $perPage = 20,
        int $page = 1,
    ): \Illuminate\Pagination\LengthAwarePaginator {
        $city   = $filters['city']   ?? null;
        $status = $filters['status'] ?? null;

        $where = "WHERE r.is_active = true AND r.race_date >= CURRENT_DATE"
            . ($city   ? " AND r.city = :city"     : '')
            . ($status ? " AND r.status = :status" : '');

        $bindings = [];
        if ($city)   $bindings['city']   = $city;
        if ($status) $bindings['status'] = $status;

        $total = DB::selectOne(
            "SELECT COUNT(*) AS cnt FROM review.races r $where",
            $bindings
        )->cnt;

        $offset = ($page - 1) * $perPage;
        $rows   = DB::select(
            "SELECT r.*, COUNT(rv.id) AS review_count,
                    ROUND(AVG(rv.rating)::numeric, 1) AS avg_rating
             FROM review.races r
             LEFT JOIN review.reviews rv ON rv.race_id = r.id
             $where
             GROUP BY r.id
             ORDER BY r.race_date ASC
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

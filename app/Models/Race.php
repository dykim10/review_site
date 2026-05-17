<?php

namespace App\Models;

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
            'distances'       => 'array',
            'is_active'       => 'boolean',
            'race_date'       => 'date',
            'reg_start'       => 'date',
            'reg_end'         => 'date',
            'ai_race_summary' => 'array',
        ];
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
     * 대회 목록 + 리뷰 수 / 평균 평점 JOIN (raw query)
     * 사용: 공개 대회 목록에서 리뷰 통계 함께 표시할 때
     */
    public static function listWithReviewStats(array $filters = []): \Illuminate\Support\Collection
    {
        $city   = $filters['city']   ?? null;
        $status = $filters['status'] ?? null;

        $sql = "
            SELECT
                r.*,
                COUNT(rv.id)          AS review_count,
                ROUND(AVG(rv.rating)::numeric, 1) AS avg_rating
            FROM review.races r
            LEFT JOIN review.reviews rv ON rv.race_id = r.id
            WHERE r.is_active = true
              AND r.race_date >= CURRENT_DATE
              " . ($city   ? "AND r.city = :city"     : '') . "
              " . ($status ? "AND r.status = :status" : '') . "
            GROUP BY r.id
            ORDER BY r.race_date ASC
        ";

        $bindings = [];
        if ($city)   $bindings['city']   = $city;
        if ($status) $bindings['status'] = $status;

        return collect(DB::select($sql, $bindings));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RaceEdition extends Model
{
    protected $table = 'review.race_editions';

    protected $fillable = [
        'race_id', 'name', 'year',
        'race_date', 'race_time', 'location', 'city',
        'is_domestic', 'country',
        'source', 'source_url',
        'weather_stn_id', 'weather_fetch_attempted_at',
        'entry_fee', 'reg_start', 'reg_end', 'status', 'is_active',
        'is_review_open',
    ];

    protected function casts(): array
    {
        return [
            'race_date'                    => 'date',
            'reg_start'                    => 'date',
            'reg_end'                      => 'date',
            'is_domestic'                  => 'boolean',
            'is_active'                    => 'boolean',
            'is_review_open'               => 'boolean',
            'weather_stn_id'               => 'integer',
            'weather_fetch_attempted_at'   => 'datetime',
            'year'                         => 'integer',
        ];
    }

    public function race()
    {
        return $this->belongsTo(Race::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'race_edition_id');
    }

    public function stats()
    {
        return $this->hasOne(RaceStats::class, 'race_edition_id');
    }

    public function weather()
    {
        return $this->hasOne(RaceWeather::class, 'race_edition_id');
    }

    public function plans()
    {
        return $this->hasMany(RacePlan::class, 'race_edition_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(EditionFeedback::class, 'race_edition_id');
    }

    public function entryCategories()
    {
        return $this->hasMany(RaceEntryCategory::class, 'race_edition_id')
            ->orderBy('sort_order');
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming';
    }

    public function isEnded(): bool
    {
        return $this->status === 'ended';
    }

    public function canWriteReview(): bool
    {
        return $this->isEnded() && $this->is_review_open;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDomestic($query)
    {
        return $query->where('is_domestic', true);
    }

    public function scopeByYear($query, ?int $year)
    {
        return $year ? $query->where('year', $year) : $query;
    }

    public function scopeUpcoming($query)
    {
        return $query->where('race_date', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('race_date', '<', now()->toDateString());
    }

    public static function listWithReviewStats(
        array $filters = [],
        int $perPage = 20,
        int $page = 1,
    ): \Illuminate\Pagination\LengthAwarePaginator {
        $isDomestic = $filters['is_domestic'] ?? null;
        $year       = $filters['year']        ?? null;
        $city       = $filters['city']        ?? null;

        $where = "WHERE re.is_active = true AND re.race_date >= CURRENT_DATE"
            . ($isDomestic !== null ? ' AND re.is_domestic = :is_domestic' : '')
            . ($year       ? ' AND re.year = :year'                        : '')
            . ($city       ? ' AND re.city = :city'                        : '');

        $bindings = [];
        if ($isDomestic !== null) {
            $bindings['is_domestic'] = (bool) $isDomestic;
        }
        if ($year) {
            $bindings['year'] = $year;
        }
        if ($city) {
            $bindings['city'] = $city;
        }

        $total = DB::selectOne(
            "SELECT COUNT(*) AS cnt FROM review.race_editions re $where",
            $bindings
        )->cnt;

        $offset = ($page - 1) * $perPage;
        $rows   = DB::select(
            "SELECT re.*, r.wa_label, r.is_certified,
                    COUNT(rv.id) AS review_count,
                    ROUND(AVG(rv.rating)::numeric, 1) AS avg_rating
             FROM review.race_editions re
             LEFT JOIN review.races r   ON r.id = re.race_id
             LEFT JOIN review.reviews rv ON rv.race_edition_id = re.id
             $where
             GROUP BY re.id, r.wa_label, r.is_certified
             ORDER BY re.race_date ASC
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

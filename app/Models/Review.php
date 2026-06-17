<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Review extends Model
{
    protected $table = 'review.reviews';

    protected $fillable = [
        'race_id', 'race_edition_id', 'user_id', 'distance',
        'rating', 'content', 'ai_summary', 'sentiment',
        'image_urls',
        'finish_time', 'course_type', 'source',
        'parsed_watch_data', 'strava_activity_id', 'gpx_url', 'is_certified',
    ];

    protected function casts(): array
    {
        return [
            'rating'            => 'integer',
            'image_urls'        => 'array',
            'parsed_watch_data' => 'array',
            'is_certified'      => 'boolean',
            'created_at'        => 'datetime',
            'updated_at'        => 'datetime',
        ];
    }

    /** 저장된 경로를 공개 URL 배열로 변환 */
    public function getImageUrls(): array
    {
        $paths = $this->image_urls ?? [];
        if (empty($paths)) return [];
        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
        return array_map(fn($p) => Storage::disk($disk)->url($p), $paths);
    }

    public function race()
    {
        return $this->belongsTo(Race::class);
    }

    public function raceEdition()
    {
        return $this->belongsTo(RaceEdition::class, 'race_edition_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hashtags()
    {
        return $this->belongsToMany(
            Hashtag::class,
            'review.review_hashtags',
            'review_id',
            'hashtag_id'
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YoutubeCache extends Model
{
    protected $table = 'review.youtube_cache';

    protected $fillable = [
        'race_edition_id', 'video_id', 'title', 'description',
        'url', 'thumbnail_url', 'view_count', 'published_at', 'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'view_count'  => 'integer',
            'published_at' => 'date',
            'fetched_at'  => 'datetime',
        ];
    }

    public function raceEdition()
    {
        return $this->belongsTo(RaceEdition::class);
    }
}

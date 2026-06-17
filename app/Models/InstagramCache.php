<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstagramCache extends Model
{
    protected $table = 'review.instagram_cache';

    protected $fillable = [
        'race_edition_id', 'post_id', 'caption', 'media_url',
        'thumbnail_url', 'like_count', 'permalink', 'posted_at', 'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'like_count' => 'integer',
            'posted_at'  => 'date',
            'fetched_at' => 'datetime',
        ];
    }

    public function raceEdition()
    {
        return $this->belongsTo(RaceEdition::class);
    }
}

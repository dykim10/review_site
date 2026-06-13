<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceStats extends Model
{
    protected $table = 'review.race_stats';
    public $timestamps = false;

    protected $fillable = [
        'race_edition_id',
        'participant_count', 'avg_finish_time_seconds', 'median_finish_time_seconds',
        'finish_time_dist', 'avg_pace_seconds',
        'sentiment_score', 'review_count',
        'training_tips', 'race_plan',
        'last_calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'finish_time_dist'            => 'array',
            'training_tips'               => 'array',
            'race_plan'                   => 'array',
            'last_calculated_at'          => 'datetime',
            'avg_finish_time_seconds'     => 'float',
            'median_finish_time_seconds'  => 'float',
            'avg_pace_seconds'            => 'float',
            'sentiment_score'             => 'float',
        ];
    }

    public function raceEdition()
    {
        return $this->belongsTo(RaceEdition::class, 'race_edition_id');
    }
}

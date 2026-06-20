<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceWeatherCase extends Model
{
    protected $table = 'review.race_weather_cases';

    protected $fillable = [
        'race_edition_id', 'review_id',
        'temperature', 'humidity', 'wind_speed',
        'finish_time_seconds', 'pace_splits', 'heart_rate_splits',
        'is_certified', 'source',
    ];

    protected function casts(): array
    {
        return [
            'temperature'         => 'float',
            'humidity'            => 'integer',
            'wind_speed'          => 'float',
            'finish_time_seconds' => 'integer',
            'pace_splits'         => 'array',
            'heart_rate_splits'   => 'array',
            'is_certified'        => 'boolean',
        ];
    }

    public function raceEdition()
    {
        return $this->belongsTo(RaceEdition::class, 'race_edition_id');
    }

    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id');
    }
}

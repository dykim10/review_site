<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceWeather extends Model
{
    protected $table = 'review.race_weather';

    protected $fillable = [
        'race_edition_id', 'stn_id', 'temperature', 'humidity',
        'wind_speed', 'wind_direction', 'precipitation',
        'weather_condition', 'raw_data', 'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'temperature'    => 'float',
            'humidity'       => 'float',
            'wind_speed'     => 'float',
            'wind_direction' => 'float',
            'precipitation'  => 'float',
            'raw_data'       => 'array',
            'fetched_at'     => 'datetime',
        ];
    }

    public function raceEdition()
    {
        return $this->belongsTo(RaceEdition::class, 'race_edition_id');
    }
}

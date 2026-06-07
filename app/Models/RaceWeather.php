<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceWeather extends Model
{
    protected $table = 'review.race_weather';

    protected $fillable = [
        'race_id', 'stn_id', 'temperature', 'humidity',
        'wind_speed', 'wind_direction', 'precipitation',
        'weather_condition', 'raw_data', 'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'temperature'   => 'float',
            'humidity'      => 'float',
            'wind_speed'    => 'float',
            'wind_direction'=> 'float',
            'precipitation' => 'float',
            'raw_data'      => 'array',
            'fetched_at'    => 'datetime',
        ];
    }

    public function race()
    {
        return $this->belongsTo(Race::class);
    }
}

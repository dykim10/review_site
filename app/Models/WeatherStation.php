<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherStation extends Model
{
    protected $table = 'review.weather_stations';

    protected $fillable = [
        'stn_id', 'stn_name', 'lat', 'lon', 'ht', 'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'stn_id'     => 'integer',
            'lat'        => 'float',
            'lon'        => 'float',
            'ht'         => 'float',
            'fetched_at' => 'datetime',
        ];
    }

    /** 셀렉트용: stn_name (stn_id) 정렬 목록 */
    public static function optionsForSelect()
    {
        return static::query()
            ->orderBy('stn_name')
            ->get(['stn_id', 'stn_name']);
    }
}

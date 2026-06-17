<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceCourse extends Model
{
    protected $table = 'review.race_courses';

    protected $fillable = [
        'race_edition_id', 'course_type', 'gpx_url',
        'elevation_data', 'segments', 'source',
        'is_certified', 'certified_at',
    ];

    protected function casts(): array
    {
        return [
            'elevation_data' => 'array',
            'segments'       => 'array',
            'is_certified'   => 'boolean',
            'certified_at'   => 'date',
        ];
    }

    public function raceEdition()
    {
        return $this->belongsTo(RaceEdition::class, 'race_edition_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RacePlan extends Model
{
    protected $table = 'review.race_plans';

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'race_edition_id', 'input', 'plan_json', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'input'      => 'array',
            'plan_json'  => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function raceEdition()
    {
        return $this->belongsTo(RaceEdition::class, 'race_edition_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceEntryCategory extends Model
{
    protected $table = 'review.race_entry_categories';

    protected $fillable = [
        'race_edition_id',
        'name',
        'distance_km',
        'entry_fee',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'distance_km' => 'decimal:3',
            'entry_fee'   => 'integer',
            'sort_order'  => 'integer',
        ];
    }

    public function edition()
    {
        return $this->belongsTo(RaceEdition::class, 'race_edition_id');
    }
}

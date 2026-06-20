<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EditionFeedback extends Model
{
    protected $table = 'review.edition_feedback';

    protected $fillable = [
        'user_id', 'race_edition_id', 'content', 'category',
    ];

    public function raceEdition()
    {
        return $this->belongsTo(RaceEdition::class, 'race_edition_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'review.reviews';

    protected $fillable = [
        'race_id', 'user_id', 'distance',
        'rating', 'content', 'ai_summary', 'sentiment',
    ];

    protected function casts(): array
    {
        return [
            'rating'     => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function race()
    {
        return $this->belongsTo(Race::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    protected $table = 'review.hashtags';

    protected $fillable = ['name', 'slug', 'source', 'usage_count'];

    public function reviews()
    {
        return $this->belongsToMany(
            Review::class,
            'review.review_hashtags',
            'hashtag_id',
            'review_id'
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Strava OAuth 토큰.
 * access_token_enc / refresh_token_enc 는 AES-128 암호화 저장 (CryptoService 사용).
 * 평문 접근이 필요할 때는 CryptoService::decrypt() 를 통해서만 복호화한다.
 */
class StravaToken extends Model
{
    protected $table = 'review.strava_tokens';

    protected $fillable = [
        'user_id', 'strava_athlete_id',
        'access_token_enc', 'refresh_token_enc', 'expires_at',
    ];

    protected $hidden = ['access_token_enc', 'refresh_token_enc'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}

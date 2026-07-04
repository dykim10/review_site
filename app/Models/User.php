<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use App\Services\CryptoService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nickname',
        'email_hash',
        'email_enc',
        'name_enc',
        'password',
        'crew_id',
        'branch_id',
        'group_id',
        'role',
        'is_beta',
        'invite_code',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getEmailAttribute(): ?string
    {
        if (isset($this->attributes['email'])) {
            return $this->attributes['email'];
        }
        return $this->email_enc ? app(CryptoService::class)->decrypt($this->email_enc) : null;
    }

    public function getNameAttribute(): ?string
    {
        if (isset($this->attributes['name'])) {
            return $this->attributes['name'];
        }
        return $this->name_enc ? app(CryptoService::class)->decrypt($this->name_enc) : null;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'is_beta'           => 'boolean',
            'password'          => 'hashed',
        ];
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * 비밀번호 재설정 토큰 키: email 컬럼 없으므로 email_hash 를 사용한다.
     * password_reset_tokens.email 컬럼에 hash 값이 저장된다.
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->email_hash ?? '';
    }
}

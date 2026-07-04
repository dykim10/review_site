<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Password;

class PasswordResetService
{
    /** 계정 존재 여부를 노출하지 않고 재설정 링크를 발송한다. */
    public function sendLinkToEmail(string $email): void
    {
        $emailHash = hash('sha256', strtolower(trim($email)));
        $user = User::where('email_hash', $emailHash)->first();

        if (!$user) {
            return;
        }

        $this->sendLinkToUser($user);
    }

    public function sendLinkToUser(User $user): void
    {
        $token = Password::broker()->getRepository()->create($user);
        $user->sendPasswordResetNotification($token);
    }
}

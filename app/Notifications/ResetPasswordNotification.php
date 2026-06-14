<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('[PAC RUN REVIEW] 비밀번호 재설정 안내')
            ->view('emails.reset-password', [
                'url'  => $url,
                'user' => $notifiable,
            ]);
    }
}

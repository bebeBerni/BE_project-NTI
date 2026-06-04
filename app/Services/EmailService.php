<?php

namespace App\Services;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginNotificationMail;
use App\Mail\PasswordChangedMail;
class EmailService
{
    public function sendWelcomeEmail(User $user): void
    {
        Mail::to($user->email)
            ->queue(new WelcomeMail($user));
    }
    public function sendLoginNotification(User $user): void
    {
        Mail::to($user->email)
            ->queue(new LoginNotificationMail($user));
    }
    public function sendPasswordChangedEmail(User $user): void
    {
        Mail::to($user->email)
            ->queue(new PasswordChangedMail($user));
    }
}

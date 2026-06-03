<?php

namespace App\Services;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function sendWelcomeEmail(User $user): void
    {
        Mail::to($user->email)
            ->queue(new WelcomeMail($user));
    }
}

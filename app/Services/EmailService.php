<?php

namespace App\Services;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginNotificationMail;
use App\Mail\PasswordChangedMail;
use App\Models\ProjectApplication;
use App\Mail\ApplicationSubmittedMail;
use App\Mail\ApplicationStatusChangedMail;
use App\Mail\MentorAssignedMail;
use App\Models\Team;
use App\Models\Mentor;

class EmailService
{
    public function sendWelcomeEmail(
        User $user,
        string $role
    ): void
    {
        Mail::to($user->email)
            ->queue(
                new WelcomeMail(
                    $user,
                    $role
                )
            );
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
    public function sendApplicationSubmittedEmail(
        User $user,
        ProjectApplication $application
    ): void
    {
        Mail::to($user->email)
            ->queue(
                new ApplicationSubmittedMail(
                    $user,
                    $application
                )
            );
    }
    public function sendApplicationStatusChangedEmail(
        User $user,
        ProjectApplication $application,
        string $oldStatus,
        string $newStatus
    ): void
    {
        Mail::to($user->email)
            ->queue(
                new ApplicationStatusChangedMail(
                    $user,
                    $application,
                    $oldStatus,
                    $newStatus
                )
            );
    }

    public function sendMentorAssignedEmail(
        User $user,
        Team $team,
        Mentor $mentor
    ): void
    {
        Mail::to($user->email)
            ->queue(
                new MentorAssignedMail(
                    $user,
                    $team,
                    $mentor
                )
            );
    }
}

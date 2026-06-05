<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeadlineReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Project $project,
        public int $daysRemaining
    ) {}

    public function build()
    {
        return $this->subject('Project Deadline Reminder')
            ->view('emails.deadline-reminder');
    }
}

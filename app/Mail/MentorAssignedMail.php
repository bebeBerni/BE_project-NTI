<?php

namespace App\Mail;

use App\Models\Mentor;
use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MentorAssignedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Team $team,
        public Mentor $mentor
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mentor Assigned to Your Team'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.mentor-assigned'
        );
    }
}

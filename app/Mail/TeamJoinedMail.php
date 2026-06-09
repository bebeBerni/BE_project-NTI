<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeamJoinedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Team $team
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Successfully Joined Team'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.team-joined'
        );
    }
}

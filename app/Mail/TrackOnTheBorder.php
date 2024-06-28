<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\Track;
use App\Models\User;

class TrackOnTheBorder extends Mailable
{
    use Queueable, SerializesModels;

    public $track;
    public $userTrack;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Track $track)
    {
        $this->track = $track;
        $this->userTrack = User::find($this->track->user_id);
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Track On The Border',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail.track-on-the-border',
            with: [
                'track' => $this->track,
                'userTrack' => $this->userTrack,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}

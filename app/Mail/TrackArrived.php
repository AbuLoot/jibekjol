<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

use App\Models\Track;
use App\Models\User;

class TrackArrived extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $track;
    public $tracks;
    public $unsubscribe;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Track $tracks
     * @return void
     */
    public function __construct($user, $tracks)
    {
        $this->user = $user;
        $this->tracks = $tracks;
        $this->unsubscribe = url(app()->getLocale().'/unsubscribe/'.Crypt::encryptString($user->email).'/'.$user->id);
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        if (count($this->tracks) > 1) {
            return new Envelope(
                subject: __('app.in_plural.your_parcels').' '.__('app.in_plural.arrived'),
            );
        }
        else {
            return new Envelope(
                subject: __('app.your_parcel').' '.__('app.statuses.arrived'),
            );
        }
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail.tracks-arrived',
            with: [
                'user' => $this->user,
                'tracks' => $this->tracks,
                'link' => $this->unsubscribe,
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

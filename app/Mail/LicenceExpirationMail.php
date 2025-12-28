<?php

namespace App\Mail;

use App\Models\Licence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LicenceExpirationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Licence $licence)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre licence sportive expire bientôt - OBD',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.licence-expiration',
        );
    }
}

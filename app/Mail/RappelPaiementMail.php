<?php

namespace App\Mail;

use App\Models\Paiement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RappelPaiementMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Paiement $paiement)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rappel de paiement - OBD',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rappel-paiement',
        );
    }
}

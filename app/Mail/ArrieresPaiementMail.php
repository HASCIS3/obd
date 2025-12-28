<?php

namespace App\Mail;

use App\Models\Athlete;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArrieresPaiementMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Athlete $athlete)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rappel: Arriérés de paiement - OBD',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.arrieres-paiement',
        );
    }
}

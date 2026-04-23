<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ImportSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly array $summary) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Import CSV — podsumowanie ' . $this->summary['imported_at']);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.import-summary');
    }
}

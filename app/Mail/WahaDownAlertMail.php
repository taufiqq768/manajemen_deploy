<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WahaDownAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $errorStatus;
    public $errorMessage;
    public $wahaUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($errorStatus, $errorMessage, $wahaUrl)
    {
        $this->errorStatus = $errorStatus;
        $this->errorMessage = $errorMessage;
        $this->wahaUrl = $wahaUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚨 ALERT: WAHA Connection Down!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.waha_down_alert',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

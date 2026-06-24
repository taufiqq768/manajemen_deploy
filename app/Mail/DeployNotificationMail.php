<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeployNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  string      $recipientName   Nama penerima
     * @param  string      $notifTitle      Judul notifikasi
     * @param  string      $notifBody       Isi pesan utama
     * @param  string|null $notifDetail     Info tambahan (alasan tolak, disetujui oleh, dll.)
     * @param  string      $notifType       'new'|'approved'|'rejected'
     * @param  int|null    $deployRequestId ID request untuk link tombol
     */
    public function __construct(
        public string  $recipientName,
        public string  $notifTitle,
        public string  $notifBody,
        public ?string $notifDetail      = null,
        public string  $notifType        = 'new',
        public ?int    $deployRequestId  = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->notifTitle);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.deploy-notification',
            with: [
                'recipientName'   => $this->recipientName,
                'title'           => $this->notifTitle,
                'body'            => $this->notifBody,
                'detail'          => $this->notifDetail,
                'bannerColor'     => $this->bannerColor(),
                'bannerIcon'      => $this->bannerIcon(),
                'detailUrl'       => $this->detailUrl(),
                'appName'         => config('app.name'),
                'appUrl'          => config('app.url'),
            ],
        );
    }

    public function detailUrl(): ?string
    {
        if (!$this->deployRequestId) return null;
        $deployRequest = \App\Models\DeployRequest::find($this->deployRequestId);
        return $deployRequest ? route('deploy-requests.show', $deployRequest) : null;
    }

    public function bannerColor(): string
    {
        return match ($this->notifType) {
            'approved' => '#16a34a',
            'rejected' => '#dc2626',
            default    => '#4f46e5',
        };
    }

    public function bannerIcon(): string
    {
        return match ($this->notifType) {
            'approved' => '✅',
            'rejected' => '❌',
            default    => '🚀',
        };
    }
}

<?php

namespace App\Services;

use App\Mail\DeployNotificationMail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /* ─────────────────────────────────────────────────────────────
     |  ENTRY POINT utama
     |  Kirim notifikasi ke satu user melalui semua saluran aktif.
     ──────────────────────────────────────────────────────────────*/

    /**
     * Kirim notifikasi ke user:
     *  - Selalu buat in-app notification (di database)
     *  - Jika user punya phone_wa → kirim WhatsApp via WAHA
     *  - Kirim email via Mailable dengan template HTML
     *
     * @param  User        $user
     * @param  int         $deployRequestId
     * @param  string      $title           Judul (juga jadi subject email)
     * @param  string      $message         Isi pesan utama
     * @param  string|null $detail          Baris keterangan tambahan
     * @param  string      $type            'new'|'approved'|'rejected'
     */
    public function send(
        User $user,
        int $deployRequestId,
        string $title,
        string $message,
        ?string $detail = null,
        string $type = 'new',
    ): void {
        // 1. In-app (selalu)
        $this->createInApp($user, $deployRequestId, $title, $message);

        // 2. WhatsApp (jika nomor tersedia)
        if (!empty($user->phone_wa)) {
            $phone = $this->normalizePhone($user->phone_wa);
            $this->sendWhatsApp($phone, $title, $message, $detail, $type, $deployRequestId);
        }

        // 3. Email via Mailable
        $this->sendEmail($user, $title, $message, $detail, $type, $deployRequestId);
    }

    /* ─────────────────────────────────────────────────────────────
     |  IN-APP
     ──────────────────────────────────────────────────────────────*/

    protected function createInApp(
        User $user,
        int $deployRequestId,
        string $title,
        string $message
    ): void {
        Notification::create([
            'user_id'           => $user->id,
            'deploy_request_id' => $deployRequestId,
            'title'             => $title,
            'message'           => $message,
            'type'              => 'in_app',
        ]);
    }

    /* ─────────────────────────────────────────────────────────────
     |  WHATSAPP via WAHA
     |  Format pesan menggunakan markdown WA:
     |   *bold*  _italic_  ~strikethrough~  ```mono```
     ──────────────────────────────────────────────────────────────*/

    protected function sendWhatsApp(
        string $phone,
        string $title,
        string $body,
        ?string $detail,
        string $type,
        int $deployRequestId,
    ): void {
        $wahaUrl     = rtrim(config('services.waha.url'), '/');
        $wahaSession = config('services.waha.session', 'default');
        $wahaApiKey  = config('services.waha.api_key');

        if (empty($wahaUrl)) {
            Log::warning('[WAHA] URL tidak dikonfigurasi, WA dilewati.', ['phone' => $phone]);
            return;
        }

        // ── Template WA ──────────────────────────────────────────
        $separator = str_repeat('─', 30);

        $lines = [];
        $lines[] = $this->waIcon($type) . " *{$title}*";
        $lines[] = $separator;
        $lines[] = '';
        $lines[] = $body;

        if ($detail) {
            $lines[] = '';
            $lines[] = "📋 _Keterangan:_";
            $lines[] = $detail;
        }

        // Tambahkan link ke sistem
        $deployRequest = \App\Models\DeployRequest::find($deployRequestId);
        $detailUrl = $deployRequest ? route('deploy-requests.show', $deployRequest) : route('dashboard');
        
        $lines[] = '';
        $lines[] = $separator;
        $lines[] = "🔗 *Lihat detail:*";
        $lines[] = $detailUrl;
        $lines[] = '';
        $lines[] = "_🕰 Deploy Manager | " . now()->timezone('Asia/Jakarta')->format('d/m/Y H:i') . " WIB_";

        $text = implode("\n", $lines);
        // ─────────────────────────────────────────────────────────

        try {
            $headers = ['Content-Type' => 'application/json'];
            if ($wahaApiKey) {
                $headers['X-Api-Key'] = $wahaApiKey;
            }

            $response = Http::withHeaders($headers)
                ->timeout(30)  // NOWEB engine butuh lebih lama
                ->post("{$wahaUrl}/api/sendText", [
                    'session' => $wahaSession,
                    'chatId'  => "{$phone}@c.us",
                    'text'    => $text,
                ]);

            if (!$response->successful()) {
                Log::warning('[WAHA] Gagal kirim WA.', [
                    'phone'  => $phone,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('[WAHA] Exception: ' . $e->getMessage(), ['phone' => $phone]);
        }
    }

    /* ─────────────────────────────────────────────────────────────
     |  EMAIL via Laravel Mailable + Blade template
     ──────────────────────────────────────────────────────────────*/

    protected function sendEmail(
        User $user,
        string $title,
        string $body,
        ?string $detail,
        string $type,
        int $deployRequestId,
    ): void {
        try {
            Mail::to($user->email, $user->name)
                ->send(new DeployNotificationMail(
                    recipientName:  $user->name,
                    notifTitle:     $title,
                    notifBody:      $body,
                    notifDetail:    $detail,
                    notifType:      $type,
                    deployRequestId: $deployRequestId,
                ));
        } catch (\Throwable $e) {
            Log::error('[Mailer] Gagal kirim email: ' . $e->getMessage(), [
                'email' => $user->email,
            ]);
        }
    }

    /* ─────────────────────────────────────────────────────────────
     |  HELPERS
     ──────────────────────────────────────────────────────────────*/

    /**
     * Normalisasi nomor WA ke format internasional tanpa + (628xxx).
     * Mendukung: 08xxx → 628xxx | 628xxx tetap | +628xxx → 628xxx
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        return $phone;
    }

    /** Ikon WA berdasarkan tipe notifikasi */
    protected function waIcon(string $type): string
    {
        return match ($type) {
            'approved' => '✅',
            'rejected' => '❌',
            default    => '🚀',
        };
    }
}

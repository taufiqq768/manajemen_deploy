<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppSyncService
{
    /**
     * Fetch daftar aplikasi dari API eksternal dan upsert ke tabel applications.
     *
     * Aturan sync:
     *  - Hanya aplikasi dengan is_active = true & is_archived = false yang diproses.
     *  - Match menggunakan kolom api_id (= field "id" dari response API).
     *  - Kolom yang diupdate: name (dari app_name), app_url (dari url), synced_at.
     *  - Jika api_id belum ada di DB → insert baru (pic_user_id = null, perlu diisi admin).
     *  - pic_user_id & deskripsi TIDAK ditimpa agar data yang sudah diisi admin tetap aman.
     */
    public function sync(): SyncResult
    {
        $apiUrl = config('services.app_api.url');

        if (empty($apiUrl)) {
            Log::warning('[AppSync] APP_API_URL tidak dikonfigurasi, sync dilewati.');
            return new SyncResult(skipped: true);
        }

        try {
            $response = Http::timeout(10)->get($apiUrl);

            if (!$response->successful()) {
                Log::error('[AppSync] API gagal diakses.', [
                    'status' => $response->status(),
                    'url'    => $apiUrl,
                ]);
                return new SyncResult(error: "HTTP {$response->status()}");
            }

            // Response: { "data": [ {...}, {...} ] }  atau langsung array
            $items = $response->json('data') ?? $response->json();

            if (!is_array($items)) {
                Log::error('[AppSync] Format response API tidak dikenali.');
                return new SyncResult(error: 'Format response tidak valid');
            }

            $created = 0;
            $updated = 0;
            $now     = now();

            foreach ($items as $item) {
                // Lewati aplikasi yang tidak aktif atau sudah diarsipkan
                if (empty($item['is_active']) || !empty($item['is_archived'])) {
                    continue;
                }

                $apiId   = (int) ($item['id'] ?? 0);
                $appName = trim($item['app_name'] ?? '');
                $appUrl  = trim($item['url'] ?? '');

                if ($apiId === 0 || $appName === '') {
                    continue; // skip data tidak valid
                }

                $existing = Application::where('api_id', $apiId)->first();

                if ($existing) {
                    // Update nama & URL dari API, jangan sentuh kolom lain
                    $existing->update([
                        'name'      => $appName,
                        'app_url'   => $appUrl ?: $existing->app_url,
                        'synced_at' => $now,
                    ]);
                    $updated++;
                } else {
                    // Insert baru — admin perlu menetapkan PIC kemudian
                    Application::create([
                        'api_id'    => $apiId,
                        'name'      => $appName,
                        'app_url'   => $appUrl,
                        'synced_at' => $now,
                        // pic_user_id sengaja null; repo_url & description kosong
                    ]);
                    $created++;
                }
            }

            Log::info("[AppSync] Selesai. Dibuat: {$created}, Diperbarui: {$updated}.");
            return new SyncResult(created: $created, updated: $updated);

        } catch (\Throwable $e) {
            Log::error('[AppSync] Exception: ' . $e->getMessage());
            return new SyncResult(error: $e->getMessage());
        }
    }
}

/**
 * Value object hasil sync — untuk dipakai controller (flash message, dll).
 */
readonly class SyncResult
{
    public function __construct(
        public int    $created = 0,
        public int    $updated = 0,
        public bool   $skipped = false,
        public ?string $error  = null,
    ) {}

    public function isOk(): bool
    {
        return $this->error === null && !$this->skipped;
    }

    public function summary(): string
    {
        if ($this->skipped) return 'Sync dilewati (API belum dikonfigurasi).';
        if ($this->error)   return "Sync gagal: {$this->error}";
        return "Sync berhasil — {$this->created} ditambahkan, {$this->updated} diperbarui.";
    }
}

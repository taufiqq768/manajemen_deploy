<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_id',
        'name',
        'description',
        'repo_url',
        'app_url',
        'version',
        'version_api_get',
        'version_api_write',
        'version_api_key',
        'version_api_write_key',
        'version_api_write_notes_key',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'synced_at' => 'datetime',
        ];
    }

    /* ── Relations ───────────────────────────────────────── */

    /** Semua deploy request untuk aplikasi ini */
    public function deployRequests()
    {
        return $this->hasMany(DeployRequest::class);
    }

    /** PIC (programmer) yang ditugaskan ke aplikasi ini */
    public function pics()
    {
        return $this->belongsToMany(User::class, 'application_user', 'application_id', 'user_id');
    }

    /** Push version to remote server via API Write */
    public function pushVersionToRemote($version = null, $releaseNotes = '')
    {
        $version = $version ?: $this->version;
        if (!$this->version_api_write) {
            return [
                'success' => false,
                'message' => 'API Write tidak dikonfigurasi untuk aplikasi ini.'
            ];
        }

        try {
            $writeKey = $this->version_api_write_key ?: 'version';
            $notesKey = $this->version_api_write_notes_key ?: 'release_notes';
            
            $payload = [
                $writeKey => $version,
                $notesKey => $releaseNotes ?: "Pembaruan versi sinkronisasi ke {$version}.",
            ];
            
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->post($this->version_api_write, $payload);
                
            if ($response->successful()) {
                VersionLog::create([
                    'application_id' => $this->id,
                    'type' => 'write',
                    'old_version' => null,
                    'new_version' => $version,
                    'status' => 'success',
                    'message' => 'Berhasil memperbarui versi di remote server via API Write.',
                    'created_at' => now(),
                ]);
                return [
                    'success' => true,
                    'message' => 'Berhasil memperbarui versi di remote server.'
                ];
            } else {
                $errorMsg = "Respon HTTP " . $response->status();
                VersionLog::create([
                    'application_id' => $this->id,
                    'type' => 'write',
                    'old_version' => null,
                    'new_version' => $version,
                    'status' => 'failed',
                    'message' => "Gagal push versi via API Write: Respon HTTP " . $response->status(),
                    'created_at' => now(),
                ]);
                return [
                    'success' => false,
                    'message' => $errorMsg
                ];
            }
        } catch (\Throwable $e) {
            VersionLog::create([
                'application_id' => $this->id,
                'type' => 'write',
                'old_version' => null,
                'new_version' => $version,
                'status' => 'failed',
                'message' => "Gagal push versi via API Write (Error Koneksi): " . $e->getMessage(),
                'created_at' => now(),
            ]);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

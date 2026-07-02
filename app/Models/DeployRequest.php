<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeployRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'application_id',
        'jenis',
        'requester_id',
        'approver_id',
        'version',
        'release_notes',
        'release_impact',
        'document_support',
        'environment',
        'status',
        'scheduled_at',
        'approved_at',
        'rejection_reason',
        'url_token',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'url_token';
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->url_token)) {
                $model->url_token = \Illuminate\Support\Str::random(10);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'scheduled_at'  => 'datetime',
            'approved_at'   => 'datetime',
            'jenis'         => 'array',
            'release_notes' => 'array',
        ];
    }

    public static function calculateBumpedVersion(string $currentVersion, array $jenis): string
    {
        $currentVersion = trim($currentVersion);
        if (empty($currentVersion) || $currentVersion === '—') {
            $currentVersion = '0.0.0';
        }
        
        if (str_starts_with(strtolower($currentVersion), 'v')) {
            $currentVersion = substr($currentVersion, 1);
        }
        
        $parts = explode('.', $currentVersion);
        while (count($parts) < 3) {
            $parts[] = '0';
        }
        
        $major = (int) $parts[0];
        $minor = (int) $parts[1];
        $patch = (int) $parts[2];
        
        if (in_array('perubahan_besar', $jenis)) {
            $major += 1;
        }
        if (in_array('perubahan_kecil', $jenis)) {
            $minor += 1;
        }
        if (in_array('bug_fixing', $jenis)) {
            $patch += 1;
        }
        
        return "{$major}.{$minor}.{$patch}";
    }

    /* ── Helpers ─────────────────────────────────────────── */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /** Label badge beserta class warna Tailwind (works on both light & dark) */
    public function statusBadge(): array
    {
        return match ($this->status) {
            'approved' => [
                'label' => 'Approved',
                'class' => 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-500/30',
            ],
            'rejected' => [
                'label' => 'Rejected',
                'class' => 'bg-red-500/15 text-red-700 dark:text-red-400 ring-1 ring-red-500/30',
            ],
            default => [
                'label' => 'Pending',
                'class' => 'bg-amber-500/15 text-amber-700 dark:text-amber-400 ring-1 ring-amber-500/30',
            ],
        };
    }

    /* ── Relations ───────────────────────────────────────── */

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /** Cek jika proses update versi ke remote server gagal */
    public function hasFailedVersionUpdate(): bool
    {
        if ($this->status !== 'approved') {
            return false;
        }

        $failed = \App\Models\VersionLog::where('application_id', $this->application_id)
            ->where('type', 'write')
            ->where('new_version', $this->version)
            ->where('status', 'failed')
            ->exists();

        if (!$failed) {
            return false;
        }

        // Jika setelah gagal ada log sukses untuk versi yang sama, anggap sudah berhasil
        $success = \App\Models\VersionLog::where('application_id', $this->application_id)
            ->where('type', 'write')
            ->where('new_version', $this->version)
            ->where('status', 'success')
            ->exists();

        return !$success;
    }

    /** Cek jika versi remote server sudah sinkron / sama dengan versi request ini */
    public function isVersionSynced(): bool
    {
        if ($this->status !== 'approved') {
            return false;
        }
        return $this->application->version === $this->version;
    }
}

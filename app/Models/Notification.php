<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'deploy_request_id',
        'title',
        'message',
        'type',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    /* ── Relations ───────────────────────────────────────── */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deployRequest()
    {
        return $this->belongsTo(DeployRequest::class);
    }

    /* ── Scopes ──────────────────────────────────────────── */

    /** Notifikasi yang belum dibaca */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /** Hanya notifikasi in-app */
    public function scopeInApp($query)
    {
        return $query->where('type', 'in_app');
    }
}

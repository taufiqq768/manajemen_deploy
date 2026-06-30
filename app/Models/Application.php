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
}

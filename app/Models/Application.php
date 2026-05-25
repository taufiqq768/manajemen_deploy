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
}

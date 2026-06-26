<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nik',
        'name',
        'email',
        'phone_wa',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* ── Helpers ─────────────────────────────────────────── */

    public function isProgrammer(): bool
    {
        return $this->role === 'programmer';
    }

    public function isProjectManager(): bool
    {
        return $this->role === 'project_manager';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /* ── Relations ───────────────────────────────────────── */

    /** Request deploy yang dibuat oleh user ini (sebagai programmer) */
    public function deployRequests()
    {
        return $this->hasMany(DeployRequest::class, 'requester_id');
    }

    /** Notifikasi untuk user ini */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /** IT Work Hub: Project dimana user ini sebagai Squad */
    public function itWhProjects()
    {
        return $this->belongsToMany(ItWhProject::class, 'it_wh_project_user', 'user_id', 'it_wh_project_id');
    }

    /** IT Work Hub: Aktivitas dimana user ini sebagai PIC */
    public function itWhActivities()
    {
        return $this->belongsToMany(ItWhActivity::class, 'it_wh_activity_user', 'user_id', 'it_wh_activity_id');
    }

    /** Jumlah notifikasi yang belum dibaca */
    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }
}

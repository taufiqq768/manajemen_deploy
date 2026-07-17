<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItWhActivity extends Model
{
    protected $fillable = [
        'it_wh_project_id',
        'type',
        'name',
        'start_date',
        'deadline',
        'adjustment_date',
        'notes',
        'document_link',
        'status_id',
        'sort_order',
    ];

    public function status()
    {
        return $this->belongsTo(ItWhMasterStatus::class, 'status_id');
    }

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
        'adjustment_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(ItWhProject::class, 'it_wh_project_id');
    }

    public function pics()
    {
        return $this->belongsToMany(User::class, 'it_wh_activity_user', 'it_wh_activity_id', 'user_id');
    }

    protected static function booted()
    {
        static::saved(function ($activity) {
            $activity->project->recalculateProgress();
        });

        static::deleted(function ($activity) {
            $activity->project->recalculateProgress();
        });
    }
}

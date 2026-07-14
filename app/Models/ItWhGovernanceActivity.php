<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItWhGovernanceActivity extends Model
{
    protected $fillable = [
        'it_wh_governance_id',
        'name',
        'start_date',
        'deadline',
        'adjustment_date',
        'notes',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
        'adjustment_date' => 'date',
    ];

    public function governance()
    {
        return $this->belongsTo(ItWhGovernance::class, 'it_wh_governance_id');
    }

    public function pics()
    {
        return $this->belongsToMany(User::class, 'it_wh_governance_activity_user', 'it_wh_governance_activity_id', 'user_id');
    }

    protected static function booted()
    {
        static::saved(function ($activity) {
            $activity->governance->recalculateProgress();
        });

        static::deleted(function ($activity) {
            $activity->governance->recalculateProgress();
        });
    }
}

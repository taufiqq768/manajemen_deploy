<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItWhGovernance extends Model
{
    protected $fillable = [
        'name',
        'description',
        'priority',
        'status',
        'progress',
        'progress_notes',
        'progress_date',
        'sort_order',
    ];

    protected $casts = [
        'progress_date' => 'date',
    ];

    public function pics()
    {
        return $this->belongsToMany(User::class, 'it_wh_governance_user', 'it_wh_governance_id', 'user_id');
    }

    public function activities()
    {
        return $this->hasMany(ItWhGovernanceActivity::class, 'it_wh_governance_id');
    }

    public function documents()
    {
        return $this->hasMany(ItWhGovernanceDocument::class, 'it_wh_governance_id');
    }

    public function recalculateProgress()
    {
        $allActivities = $this->activities()->get();
        if ($allActivities->count() > 0) {
            $totalWeight = 0;
            foreach ($allActivities as $act) {
                $weight = match($act->status) {
                    'On Progress' => 50,
                    'Hold' => 50, // Keep progress if on hold
                    'Done' => 100,
                    default => 0, // Not Started
                };
                $totalWeight += $weight;
            }
            $this->progress = (int) round($totalWeight / $allActivities->count());
        } else {
            $this->progress = 0;
        }
        $this->save();
    }
}

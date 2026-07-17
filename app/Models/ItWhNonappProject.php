<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItWhNonappProject extends Model
{
    protected $fillable = [
        'name',
        'description',
        'priority',
        'status_id',
        'bpo_division_id',
        'progress',
        'pain_point_uraian',
        'pain_point_impact',
        'start_date',
        'deadline',
        'adjustment_date',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
        'adjustment_date' => 'date',
    ];

    public function status()
    {
        return $this->belongsTo(ItWhMasterStatus::class, 'status_id');
    }

    public function bpoDivision()
    {
        return $this->belongsTo(ItWhMasterDivision::class, 'bpo_division_id');
    }

    public function squads()
    {
        return $this->belongsToMany(User::class, 'it_wh_nonapp_project_user', 'it_wh_nonapp_project_id', 'user_id');
    }

    public function activities()
    {
        return $this->hasMany(ItWhNonappActivity::class, 'it_wh_nonapp_project_id');
    }

    public function documents()
    {
        return $this->hasMany(ItWhNonappDocument::class, 'it_wh_nonapp_project_id');
    }

    public function recalculateProgress()
    {
        $allActivities = $this->activities()->with('status')->get();
        if ($allActivities->count() > 0) {
            $totalWeight = 0;
            foreach ($allActivities as $act) {
                $weight = $act->status ? $act->status->weight : 0;
                $totalWeight += $weight;
            }
            $this->progress = (int) round($totalWeight / $allActivities->count());
        } else {
            $this->progress = 0;
        }
        $this->save();
    }
}

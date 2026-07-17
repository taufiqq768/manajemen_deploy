<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItWhProject extends Model
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
        'sort_order'
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
        return $this->belongsToMany(User::class, 'it_wh_project_user', 'it_wh_project_id', 'user_id');
    }

    public function activities()
    {
        return $this->hasMany(ItWhActivity::class, 'it_wh_project_id');
    }

    public function documents()
    {
        return $this->hasMany(ItWhProjectDocument::class, 'it_wh_project_id');
    }

    public function groups()
    {
        return $this->belongsToMany(ItWhProjectGroup::class, 'it_wh_project_group_project', 'it_wh_project_id', 'it_wh_project_group_id');
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

        foreach ($this->groups as $group) {
            $group->recalculateProgress();
        }
    }
}

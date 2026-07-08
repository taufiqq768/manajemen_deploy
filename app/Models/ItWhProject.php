<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItWhProject extends Model
{
    protected $fillable = [
        'name',
        'description',
        'priority',
        'status',
        'bpo',
        'progress',
        'pain_point_uraian',
        'pain_point_impact',
        'sort_order'
    ];

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
        $allActivities = $this->activities()->get();
        if ($allActivities->count() > 0) {
            $totalWeight = 0;
            foreach ($allActivities as $act) {
                $weight = match($act->status) {
                    'Ureq Analysis' => 15,
                    'Programming' => 50,
                    'Tech Testing' => 70,
                    'SIT' => 85,
                    'UAT' => 95,
                    'Done' => 100,
                    default => 0,
                };
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

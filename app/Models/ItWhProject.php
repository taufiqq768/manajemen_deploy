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
}

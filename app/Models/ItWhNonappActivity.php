<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItWhNonappActivity extends Model
{
    protected $fillable = [
        'it_wh_nonapp_project_id',
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

    public function project()
    {
        return $this->belongsTo(ItWhNonappProject::class, 'it_wh_nonapp_project_id');
    }

    public function pics()
    {
        return $this->belongsToMany(User::class, 'it_wh_nonapp_activity_user', 'it_wh_nonapp_activity_id', 'user_id');
    }
}

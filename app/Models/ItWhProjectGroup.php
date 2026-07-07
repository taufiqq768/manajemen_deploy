<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItWhProjectGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'progress',
        'deadline',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function projects()
    {
        return $this->belongsToMany(ItWhProject::class, 'it_wh_project_group_project', 'it_wh_project_group_id', 'it_wh_project_id');
    }
}

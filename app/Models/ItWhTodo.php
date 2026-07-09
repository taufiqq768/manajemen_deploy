<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItWhTodo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assigner_id',
        'date',
        'task_name',
        'deadline',
        'status',
        'notes',
        'sort_order',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function assigner()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigner_id');
    }
}

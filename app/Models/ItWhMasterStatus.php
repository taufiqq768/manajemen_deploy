<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItWhMasterStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'weight',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'weight' => 'integer',
        'sort_order' => 'integer',
    ];
}

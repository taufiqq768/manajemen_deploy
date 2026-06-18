<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WahaConnectionLog extends Model
{
    protected $fillable = ['status', 'response_time_ms', 'error_message'];
}

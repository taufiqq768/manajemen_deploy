<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeployRequestDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'deploy_request_id',
        'document_number',
        'file_path',
    ];

    public function deployRequest()
    {
        return $this->belongsTo(DeployRequest::class);
    }
}

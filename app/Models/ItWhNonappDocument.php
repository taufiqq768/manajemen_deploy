<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItWhNonappDocument extends Model
{
    protected $fillable = [
        'it_wh_nonapp_project_id',
        'description',
        'document_date',
        'file_path',
        'link',
    ];

    protected $casts = [
        'document_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(ItWhNonappProject::class, 'it_wh_nonapp_project_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItWhProjectDocument extends Model
{
    use HasFactory;

    protected $table = 'it_wh_project_documents';

    protected $fillable = [
        'it_wh_project_id',
        'type', // 'PIR' or 'Dokumen'
        'description',
        'document_date',
        'file_path',
        'link'
    ];

    public function project()
    {
        return $this->belongsTo(ItWhProject::class, 'it_wh_project_id');
    }
}

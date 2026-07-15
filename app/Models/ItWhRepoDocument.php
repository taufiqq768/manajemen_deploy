<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItWhRepoDocument extends Model
{
    protected $fillable = [
        'it_wh_repo_type_id',
        'it_wh_repo_sub_type_id',
        'name',
        'version',
        'document_date',
        'file_path',
        'link',
        'sort_order',
    ];

    protected $casts = [
        'document_date' => 'date',
    ];

    public function type()
    {
        return $this->belongsTo(ItWhRepoType::class, 'it_wh_repo_type_id');
    }

    public function subType()
    {
        return $this->belongsTo(ItWhRepoSubType::class, 'it_wh_repo_sub_type_id');
    }
}

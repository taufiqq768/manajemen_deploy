<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItWhRepoSubType extends Model
{
    protected $fillable = ['it_wh_repo_type_id', 'name', 'description', 'sort_order'];

    public function type()
    {
        return $this->belongsTo(ItWhRepoType::class, 'it_wh_repo_type_id');
    }

    public function documents()
    {
        return $this->hasMany(ItWhRepoDocument::class, 'it_wh_repo_sub_type_id')->orderBy('sort_order');
    }
}

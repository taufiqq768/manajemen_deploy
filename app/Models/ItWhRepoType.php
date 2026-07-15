<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItWhRepoType extends Model
{
    protected $fillable = ['name', 'description', 'sort_order'];

    public function subTypes()
    {
        return $this->hasMany(ItWhRepoSubType::class, 'it_wh_repo_type_id')->orderBy('sort_order');
    }

    // Documents directly under this type (no sub_type)
    public function documents()
    {
        return $this->hasMany(ItWhRepoDocument::class, 'it_wh_repo_type_id')
            ->whereNull('it_wh_repo_sub_type_id')
            ->orderBy('sort_order');
    }

    // All documents under this type (including those in sub types)
    public function allDocuments()
    {
        return $this->hasMany(ItWhRepoDocument::class, 'it_wh_repo_type_id')->orderBy('sort_order');
    }
}

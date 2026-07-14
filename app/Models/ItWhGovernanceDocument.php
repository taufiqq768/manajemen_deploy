<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItWhGovernanceDocument extends Model
{
    protected $fillable = [
        'it_wh_governance_id',
        'description',
        'document_date',
        'file_path',
        'link',
    ];

    protected $casts = [
        'document_date' => 'date',
    ];

    public function governance()
    {
        return $this->belongsTo(ItWhGovernance::class, 'it_wh_governance_id');
    }
}

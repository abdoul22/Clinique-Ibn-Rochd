<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecapitulatifServiceJournalier extends Model
{

    protected $fillable = ['idsvc', 'total', 'date'];
    
    public function service()
    {
        return $this->belongsTo(Service::class, 'idsvc');
    }
}

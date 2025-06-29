<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModePaiement extends Model
{
    protected $fillable = ['caisse_id', 'type', 'montant','sedad'];

    public function caisse()
    {
        return $this->belongsTo(Caisse::class);
    }
}

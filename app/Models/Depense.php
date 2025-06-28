<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'montant', 'etat_caisse_id', 'mode_paiement_id', 'source'];


    public function etatCaisse()
    {
        return $this->belongsTo(EtatCaisse::class);
    }
    public function mode_paiement()
    {
        return $this->belongsTo(ModePaiement::class, 'mode_paiement_id');
    }
}

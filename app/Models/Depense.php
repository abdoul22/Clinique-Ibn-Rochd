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

    // Note: mode_paiement_id est maintenant une chaîne, pas une relation
    // Si vous voulez une relation, vous devrez créer une méthode personnalisée
    public function getModePaiementAttribute()
    {
        return $this->mode_paiement_id;
    }
}

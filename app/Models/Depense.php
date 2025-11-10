<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'montant', 'etat_caisse_id', 'mode_paiement_id', 'source', 'credit_id', 'rembourse', 'created_by', 'created_at', 'updated_at'];


    public function etatCaisse()
    {
        return $this->belongsTo(EtatCaisse::class);
    }

    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }

    // Relation avec le mode de paiement via le type
    public function modePaiement()
    {
        return $this->belongsTo(ModePaiement::class, 'mode_paiement_id', 'type');
    }

    // Relation avec l'utilisateur qui a créé la dépense
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accesseur pour obtenir le mode de paiement
    public function getModePaiementAttribute()
    {
        return $this->mode_paiement_id;
    }
}

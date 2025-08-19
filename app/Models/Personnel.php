<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Personnel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'fonction',
        'adresse',
        'telephone',
        'salaire',
        'is_approved',
        'created_by',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function etatsDeCaisseCredit()
    {
        return $this->hasMany(EtatCaisse::class, 'credit_personnel');
    }
    public function credits()
    {
        return $this->morphMany(Credit::class, 'source');
    }
    public function getTotalCreditsAttribute()
    {
        return $this->credits()->sum('montant');
    }
    public function updateCredit()
    {
        $creditActif = $this->credits()->sum('montant') - $this->credits()->sum('montant_paye');
        $this->update(['credit' => $creditActif]);
    }
    public function getCreditAttribute()
    {
        return $this->credits()->sum('montant') - $this->credits()->sum('montant_paye');
    }

    public function getStatutCreditAttribute()
    {
        $credits = $this->credits;

        if ($credits->isEmpty()) {
            return null;
        }

        if ($credits->every(fn($c) => $c->status === 'payé')) {
            return 'payé';
        }

        if ($credits->contains(fn($c) => $c->status === 'partiellement payé')) {
            return 'partiellement payé';
        }

        return 'non payé';
    }

    public function getStatutColorAttribute()
    {
        return match ($this->statut_credit) {
            'payé' => 'text-green-600',
            'partiellement payé' => 'text-yellow-500',
            'non payé' => 'text-red-600',
            default => 'text-gray-400', // aucun crédit
        };
    }

    /**
     * Vérifier si le personnel peut prendre un crédit
     */
    public function peutPrendreCredit($montantDemande)
    {
        $creditActuel = $this->credit;
        $nouveauCreditTotal = $creditActuel + $montantDemande;

        return $nouveauCreditTotal <= $this->salaire;
    }

    /**
     * Obtenir le montant maximum de crédit possible
     */
    public function getMontantMaxCreditAttribute()
    {
        return $this->salaire - $this->credit;
    }

    /**
     * Déduire automatiquement le crédit du salaire (fin de mois)
     */
    public function deduireCreditDuSalaire()
    {
        $creditADeduire = $this->credit;

        if ($creditADeduire > 0) {
            // Mettre à jour le salaire
            $this->update([
                'salaire' => $this->salaire - $creditADeduire,
                'credit' => 0 // Remettre le crédit à 0
            ]);

            // Marquer tous les crédits comme payés
            $this->credits()->where('status', '!=', 'payé')->update([
                'status' => 'payé',
                'montant_paye' => DB::raw('montant')
            ]);

            return $creditADeduire;
        }

        return 0;
    }

    /**
     * Obtenir le salaire net après déduction des crédits
     */
    public function getSalaireNetAttribute()
    {
        return $this->salaire - $this->credit;
    }

    /**
     * Vérifier si le personnel a des crédits en cours
     */
    public function aDesCreditsEnCours()
    {
        return $this->credit > 0;
    }
}

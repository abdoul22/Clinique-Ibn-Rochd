<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assurance extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'credit'];

    public function etatsDeCaisse()
    {
        return $this->hasMany(EtatCaisse::class, 'assurance_id');
    }
    public function getCreditFormatAttribute()
    {
        return number_format($this->credit, 0, ',', ' ') . ' MRU';
    }
    public function caisse()
    {
        return $this->hasMany(Caisse::class);
    }
    public function credits()
    {
        return $this->morphMany(Credit::class, 'source');
    }

    /**
     * Tarifs spécifiques pour les examens
     */
    public function examenTarifs()
    {
        return $this->hasMany(\App\Models\ExamenAssuranceTarif::class, 'assurance_id');
    }

    /**
     * Mettre à jour le crédit total de l'assurance
     */
    public function updateCredit()
    {
        $creditActif = $this->credits()->sum('montant') - $this->credits()->sum('montant_paye');
        $this->update(['credit' => $creditActif]);
    }

    /**
     * Obtenir le crédit actuel (calculé dynamiquement)
     */
    public function getCreditAttribute()
    {
        return $this->credits()->sum('montant') - $this->credits()->sum('montant_paye');
    }

    /**
     * Obtenir le statut du crédit
     */
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

    /**
     * Obtenir la couleur du statut
     */
    public function getStatutColorAttribute()
    {
        return match ($this->statut_credit) {
            'payé' => 'text-green-600',
            'partiellement payé' => 'text-yellow-500',
            'non payé' => 'text-red-600',
            default => 'text-gray-400', // aucun crédit
        };
    }
}

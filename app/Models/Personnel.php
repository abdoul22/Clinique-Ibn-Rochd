<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'fonction',
        'adresse',
        'telephone',
        'salaire'
    ];

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
}

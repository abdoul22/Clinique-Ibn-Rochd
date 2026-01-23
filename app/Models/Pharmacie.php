<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pharmacie extends Model
{
    protected $fillable = [
        'nom_medicament',
        'prix_achat',
        'prix_vente',
        'prix_unitaire',
        'quantite',
        'stock',
        'description',
        'categorie',
        'fournisseur',
        'date_expiration',
        'statut'
    ];

    protected $casts = [
        'prix_achat' => 'decimal:2',
        'prix_vente' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
        'date_expiration' => 'date',
    ];

    /**
     * Boot method pour les événements
     */
    protected static function boot()
    {
        parent::boot();

        // Créer automatiquement un examen lors de la création d'un médicament
        static::created(function ($pharmacie) {
            // Récupérer ou créer un service PHARMACIE générique
            $servicePharmacie = \App\Models\Service::where('type_service', 'PHARMACIE')
                ->whereNull('pharmacie_id')
                ->first();
            
            if (!$servicePharmacie) {
                $servicePharmacie = \App\Models\Service::create([
                    'nom' => 'Pharmacie',
                    'type_service' => 'PHARMACIE',
                    'description' => 'Service générique pour les médicaments',
                ]);
            }
            
            // Créer un examen pour ce médicament
            \App\Models\Examen::create([
                'nom' => $pharmacie->nom_medicament,
                'idsvc' => $servicePharmacie->id,
                'tarif' => $pharmacie->prix_vente,
                'part_cabinet' => $pharmacie->prix_vente,
                'part_medecin' => 0,
            ]);
        });

        // Quand un médicament est supprimé, supprimer les examens liés
        static::deleting(function ($pharmacie) {
            // Récupérer le service PHARMACIE générique
            $servicePharmacie = \App\Models\Service::where('type_service', 'PHARMACIE')
                ->whereNull('pharmacie_id')
                ->first();
            
            if ($servicePharmacie) {
                // Supprimer les examens avec le même nom dans le service PHARMACIE
                \App\Models\Examen::where('nom', $pharmacie->nom_medicament)
                    ->where('idsvc', $servicePharmacie->id)
                    ->delete();
            }
            
            // Supprimer les services liés (ancienne logique)
            $pharmacie->services()->delete();

            // Supprimer les examens liés aux services de ce médicament
            $serviceIds = $pharmacie->services()->pluck('id');
            if ($serviceIds->count() > 0) {
                \App\Models\Examen::whereIn('idsvc', $serviceIds)->delete();
            }
        });

        // Quand un médicament est mis à jour, mettre à jour l'examen correspondant
        static::updated(function ($pharmacie) {
            // Récupérer le service PHARMACIE générique
            $servicePharmacie = \App\Models\Service::where('type_service', 'PHARMACIE')
                ->whereNull('pharmacie_id')
                ->first();
            
            if ($servicePharmacie) {
                // Mettre à jour l'examen correspondant
                \App\Models\Examen::where('nom', $pharmacie->nom_medicament)
                    ->where('idsvc', $servicePharmacie->id)
                    ->update([
                        'tarif' => $pharmacie->prix_vente,
                        'part_cabinet' => $pharmacie->prix_vente,
                        'part_medecin' => 0,
                    ]);
            }
        });
    }

    /**
     * Relation avec les services
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Vérifier si le stock est suffisant
     */
    public function stockSuffisant(int $quantite): bool
    {
        return $this->stock >= $quantite;
    }

    /**
     * Déduire du stock
     */
    public function deduireStock(int $quantite): bool
    {
        if ($this->stockSuffisant($quantite)) {
            $this->stock -= $quantite;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Ajouter au stock
     */
    public function ajouterStock(int $quantite): void
    {
        $this->stock += $quantite;
        $this->save();
    }

    /**
     * Calculer la marge bénéficiaire
     */
    public function getMargeBeneficiaireAttribute(): float
    {
        return $this->prix_vente - $this->prix_achat;
    }

    /**
     * Vérifier si le médicament est en rupture
     */
    public function getEnRuptureAttribute(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Vérifier si le médicament expire bientôt (dans les 180 jours / 6 mois)
     */
    public function getExpireBientotAttribute(): bool
    {
        if (!$this->date_expiration) {
            return false;
        }
        // Vérifier si la date d'expiration est dans moins de 180 jours (6 mois)
        return $this->date_expiration->isFuture() && $this->date_expiration->diffInDays(now()) <= 180;
    }

    /**
     * Scope pour les médicaments actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Scope pour les médicaments en stock
     */
    public function scopeEnStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope pour les médicaments en rupture
     */
    public function scopeEnRupture($query)
    {
        return $query->where('stock', '<=', 0);
    }
}

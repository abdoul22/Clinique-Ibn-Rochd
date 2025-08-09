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

        // Ne plus créer automatiquement un service lors de la création d'un médicament
        static::created(function ($pharmacie) {
            // Intentionnellement vide pour éviter les créations automatiques dans services
        });

        // Quand un médicament est supprimé, supprimer automatiquement les services et examens liés
        static::deleting(function ($pharmacie) {
            // Supprimer les services liés
            $pharmacie->services()->delete();

            // Supprimer les examens liés aux services de ce médicament
            $serviceIds = $pharmacie->services()->pluck('id');
            if ($serviceIds->count() > 0) {
                \App\Models\Examen::whereIn('idsvc', $serviceIds)->delete();
            }
        });

        // Quand un médicament est mis à jour, mettre à jour le service correspondant
        static::updated(function ($pharmacie) {
            // Ne plus synchroniser automatiquement avec services
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
     * Vérifier si le médicament expire bientôt (dans les 30 jours)
     */
    public function getExpireBientotAttribute(): bool
    {
        if (!$this->date_expiration) {
            return false;
        }
        return $this->date_expiration->diffInDays(now()) <= 30;
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

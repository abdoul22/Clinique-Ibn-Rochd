<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medicament extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'forme',
        'dosage',
        'fabricant',
        'description',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Relation avec les lignes d'ordonnances
     */
    public function ordonnanceMedicaments()
    {
        return $this->hasMany(OrdonnanceMedicament::class);
    }

    /**
     * Scope pour les médicaments actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Recherche par nom
     */
    public function scopeRechercheParNom($query, $term)
    {
        return $query->where('nom', 'like', '%' . $term . '%')
                     ->orWhere('forme', 'like', '%' . $term . '%');
    }

    /**
     * Accesseur pour le nom complet (nom + forme)
     */
    public function getNomCompletAttribute()
    {
        if ($this->forme) {
            return $this->nom . ' - ' . $this->forme;
        }
        return $this->nom;
    }
}


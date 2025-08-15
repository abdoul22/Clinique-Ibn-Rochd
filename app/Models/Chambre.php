<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chambre extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'type',
        'etage',
        'statut',
        'capacite_lits',
        'tarif_journalier',
        'description',
        'equipements',
    ];

    protected $casts = [
        'tarif_journalier' => 'decimal:2',
        'capacite_lits' => 'integer',
    ];

    public function lits()
    {
        return $this->hasMany(Lit::class);
    }

    public function hospitalisations()
    {
        return $this->hasManyThrough(Hospitalisation::class, Lit::class);
    }

    // Méthodes utilitaires
    public function getLitsLibresAttribute()
    {
        return $this->lits()->where('statut', 'libre')->count();
    }

    public function getLitsOccupesAttribute()
    {
        return $this->lits()->where('statut', 'occupe')->count();
    }

    public function getTauxOccupationAttribute()
    {
        $totalLits = $this->lits()->count();
        if ($totalLits === 0) return 0;

        return round(($this->lits_occupes / $totalLits) * 100, 2);
    }

    public function getNomCompletAttribute()
    {
        $nom = $this->nom;
        if ($this->etage) {
            $nom .= ' (Étage ' . $this->etage . ')';
        }
        return $nom;
    }

    // Scopes pour filtrage
    public function scopeActive($query)
    {
        return $query->where('statut', 'active');
    }

    public function scopeLibre($query)
    {
        return $query->whereHas('lits', function ($q) {
            $q->where('statut', 'libre');
        });
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }
}

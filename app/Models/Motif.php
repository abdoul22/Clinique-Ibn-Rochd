<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motif extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'actif'
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Obtenir les motifs actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Relation avec les rendez-vous
     */
    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'motif', 'nom');
    }
}

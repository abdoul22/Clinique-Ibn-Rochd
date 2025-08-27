<?php

// app/Models/Medecin.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medecin extends Model
{
    use HasFactory;

    protected $table = 'medecins';

    protected $fillable = [
        'nom',
        'fonction',
        'prenom',
        'specialite',
        'telephone',
        'email',
        'statut',
    ];

    /**
     * Accesseur pour obtenir le nom complet avec la fonction
     */
    public function getNomCompletAttribute()
    {
        $fonctions = [
            'Pr' => 'Pr.',
            'Dr' => 'Dr.',
            'Tss' => 'Tss.',
            'SGF' => 'SGF.',
            'IDE' => 'IDE.'
        ];

        $prefix = $fonctions[$this->fonction] ?? 'Dr.';
        return $prefix . ' ' . $this->nom;
    }

    /**
     * Accesseur pour obtenir le libellé complet de la fonction
     */
    public function getFonctionCompletAttribute()
    {
        $fonctions = [
            'Pr' => 'Professeur',
            'Dr' => 'Docteur',
            'Tss' => 'Technicien Supérieur',
            'SGF' => 'Sage femme',
            'IDE' => 'Infirmier d\'état'
        ];

        return $fonctions[$this->fonction] ?? 'Docteur';
    }

    public function recapitulatifOperateurs()
    {
        return $this->hasMany(RecapitulatifOperateur::class);
    }
    public function caisses()
    {
        return $this->hasMany(\App\Models\Caisse::class);
    }
    public function examens()
    {
        return $this->hasMany(Examen::class);
    }
    // Medecin.php

    public function etatsCaisse()
    {
        return $this->hasMany(EtatCaisse::class);
    }

    // Relation avec les rendez-vous
    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class);
    }
}

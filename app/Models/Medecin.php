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
            'IDE' => 'IDE.',
            'Phr' => 'Phr.'
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
            'IDE' => 'Infirmier d\'état',
            'Phr' => 'Pharmacien'
        ];

        return $fonctions[$this->fonction] ?? 'Docteur';
    }

    /**
     * Accesseur pour obtenir le nom complet avec prénom en premier
     */
    public function getNomCompletAvecPrenomAttribute()
    {
        $fonctions = [
            'Pr' => 'Pr.',
            'Dr' => 'Dr.',
            'Tss' => 'Tss.',
            'SGF' => 'SGF.',
            'IDE' => 'IDE.',
            'Phr' => 'Phr.'
        ];

        $prefix = $fonctions[$this->fonction] ?? 'Dr.';
        return $prefix . ' ' . $this->prenom . ' ' . $this->nom;
    }

    /**
     * Accesseur pour obtenir le nom complet avec spécialité pour les selects
     */
    public function getNomCompletAvecSpecialiteAttribute()
    {
        $fonctions = [
            'Pr' => 'Pr.',
            'Dr' => 'Dr.',
            'Tss' => 'Tss.',
            'SGF' => 'SGF.',
            'IDE' => 'IDE.',
            'Phr' => 'Phr.'
        ];

        $prefix = $fonctions[$this->fonction] ?? 'Dr.';
        $nomComplet = $prefix . ' ' . $this->prenom . ' ' . $this->nom;

        if ($this->specialite) {
            return $nomComplet . ' - ' . $this->specialite;
        }

        return $nomComplet;
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

    // Relation avec les utilisateurs (médecins ayant un compte)
    public function user()
    {
        return $this->hasOne(User::class);
    }

    // Relation avec les consultations
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    // Relation avec les ordonnances
    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class);
    }
}

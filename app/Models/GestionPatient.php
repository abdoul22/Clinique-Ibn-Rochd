<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GestionPatient extends Model
{

    use HasFactory;
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'age',
        'gender',
        'phone',
    ];

    // Relation avec les rendez-vous
    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'patient_id');
    }

    // Relation avec les examens (caisses)
    public function caisses()
    {
        return $this->hasMany(Caisse::class, 'gestion_patient_id');
    }

    // Relation avec les dossiers médicaux
    public function dossierMedical()
    {
        return $this->hasOne(DossierMedical::class, 'patient_id');
    }

    // Accesseurs pour compatibilité avec les vues
    public function getNomAttribute()
    {
        return $this->last_name;
    }

    public function getPrenomAttribute()
    {
        return $this->first_name;
    }

    // Accesseur pour calculer l'âge à partir de la date de naissance
    public function getCalculatedAgeAttribute()
    {
        if ($this->date_of_birth) {
            return \Carbon\Carbon::parse($this->date_of_birth)->age;
        }
        return $this->age;
    }

    // Accesseur pour obtenir l'âge (priorité au champ age, sinon calculé)
    public function getAgeAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }

        if ($this->date_of_birth) {
            return \Carbon\Carbon::parse($this->date_of_birth)->age;
        }

        return null;
    }

    // Mutateur pour calculer la date de naissance à partir de l'âge
    public function setAgeAttribute($value)
    {
        $this->attributes['age'] = $value;

        // Si on a un âge mais pas de date de naissance, calculer une date approximative
        if ($value && !$this->date_of_birth) {
            $this->attributes['date_of_birth'] = \Carbon\Carbon::now()->subYears($value)->format('Y-m-d');
        }
    }

    // Mutateur pour mettre à jour l'âge quand la date de naissance change
    public function setDateOfBirthAttribute($value)
    {
        $this->attributes['date_of_birth'] = $value;

        // Mettre à jour l'âge si on a une date de naissance
        if ($value) {
            $this->attributes['age'] = \Carbon\Carbon::parse($value)->age;
        }
    }
}

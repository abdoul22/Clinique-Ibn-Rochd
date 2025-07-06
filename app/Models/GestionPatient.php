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
}

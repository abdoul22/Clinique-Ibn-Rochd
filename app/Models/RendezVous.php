<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'date_rdv',
        'heure_rdv',
        'motif',
        'statut',
        'notes',
        'numero_entree',
        'created_by',
    ];

    protected $casts = [
        'date_rdv' => 'date',
        'heure_rdv' => 'datetime',
    ];

    // Relation avec le patient
    public function patient()
    {
        return $this->belongsTo(GestionPatient::class, 'patient_id');
    }

    // Relation avec le médecin
    public function medecin()
    {
        return $this->belongsTo(Medecin::class, 'medecin_id');
    }

    // Relation avec l'utilisateur qui a créé le rendez-vous
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accesseur pour le nom complet du patient
    public function getPatientNomCompletAttribute()
    {
        return $this->patient ? $this->patient->first_name . ' ' . $this->patient->last_name : 'N/A';
    }

    // Accesseur pour le nom complet du médecin
    public function getMedecinNomCompletAttribute()
    {
        return $this->medecin ? $this->medecin->nom . ' ' . $this->medecin->prenom : 'N/A';
    }
}

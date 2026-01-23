<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'dossier_medical_id',
        'date_consultation',
        'heure_consultation',
        'motif',
        'antecedents',
        'histoire_maladie',
        'examen_clinique',
        'conduite_tenir',
        'resume',
        'statut',
    ];

    protected $casts = [
        'date_consultation' => 'date',
        'heure_consultation' => 'datetime',
    ];

    /**
     * Relation avec le patient
     */
    public function patient()
    {
        return $this->belongsTo(GestionPatient::class, 'patient_id');
    }

    /**
     * Relation avec le médecin
     */
    public function medecin()
    {
        return $this->belongsTo(Medecin::class, 'medecin_id');
    }

    /**
     * Relation avec le dossier médical
     */
    public function dossierMedical()
    {
        return $this->belongsTo(DossierMedical::class, 'dossier_medical_id');
    }

    /**
     * Relation avec les ordonnances
     */
    public function ordonnances()
    {
        return $this->hasMany(Ordonnance::class);
    }

    /**
     * Scope pour les consultations d'un médecin
     */
    public function scopeParMedecin($query, $medecinId)
    {
        return $query->where('medecin_id', $medecinId);
    }

    /**
     * Scope pour les consultations d'un patient
     */
    public function scopeParPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Scope pour les consultations terminées
     */
    public function scopeTerminees($query)
    {
        return $query->where('statut', 'terminee');
    }
}


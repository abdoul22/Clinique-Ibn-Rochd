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

    // Relation avec les caisses (pour vérifier si le rendez-vous a été payé)
    public function caisses()
    {
        return $this->hasMany(Caisse::class, 'numero_entre', 'numero_entree')
            ->where('medecin_id', $this->medecin_id)
            ->where('gestion_patient_id', $this->patient_id)
            ->whereDate('created_at', $this->date_rdv);
    }

    // Méthode pour vérifier si le rendez-vous a été payé
    public function isPaid()
    {
        return $this->caisses()->exists();
    }

    // Méthode pour récupérer la facture associée
    public function getFacture()
    {
        return $this->caisses()->first();
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class DossierMedical extends Model
{
    use HasFactory;

    protected $table = 'dossiers_medicaux';

    protected $fillable = [
        'patient_id',
        'numero_dossier',
        'date_creation',
        'derniere_visite',
        'nombre_visites',
        'total_depense',
        'statut',
        'notes_generales',
    ];

    protected $casts = [
        'date_creation' => 'date',
        'derniere_visite' => 'date',
        'total_depense' => 'decimal:2',
    ];

    // Relation avec le patient
    public function patient()
    {
        return $this->belongsTo(GestionPatient::class, 'patient_id');
    }

    // Relation avec les examens (via caisse)
    public function examens()
    {
        return $this->hasManyThrough(Caisse::class, GestionPatient::class, 'id', 'gestion_patient_id', 'patient_id');
    }

    // Relation avec les rendez-vous
    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'patient_id', 'patient_id');
    }

    // Relation avec les consultations
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    // Méthode pour calculer les statistiques du dossier
    public function calculerStatistiques()
    {
        $examens = $this->examens()->with(['medecin', 'examen', 'service'])->get();
        $rendezVous = $this->rendezVous()->with(['medecin'])->get();

        return [
            'total_examens' => $examens->count(),
            'total_rendez_vous' => $rendezVous->count(),
            'rendez_vous_confirmes' => $rendezVous->where('statut', 'confirme')->count(),
            'rendez_vous_termines' => $rendezVous->where('statut', 'termine')->count(),
            'total_depense' => $examens->sum('total'),
            'derniere_visite' => $examens->max('date_examen'),
            'medecins_consultes' => $examens->pluck('medecin.nom')->unique()->count(),
        ];
    }

    // Méthode pour mettre à jour automatiquement le dossier
    public function mettreAJour()
    {
        $examens = $this->examens();
        $rendezVous = $this->rendezVous();

        $this->update([
            'nombre_visites' => $examens->count(),
            'total_depense' => $examens->sum('total'),
            'derniere_visite' => $examens->max('date_examen'),
        ]);

        return $this;
    }

    // Accesseur pour le nom complet du patient
    public function getPatientNomCompletAttribute()
    {
        return $this->patient ? $this->patient->first_name . ' ' . $this->patient->last_name : 'N/A';
    }

    // Scope pour les dossiers actifs
    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    // Scope pour les dossiers avec visites récentes
    public function scopeAvecVisitesRecentes($query, $jours = 30)
    {
        return $query->where('derniere_visite', '>=', Carbon::now()->subDays($jours));
    }
}

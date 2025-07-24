<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Caisse extends Model
{
    use HasFactory;

    // app/Models/Caisse.php
    protected $casts = [
        'date_examen' => 'date',
    ];

    protected $fillable = [
        'numero_entre',
        'gestion_patient_id', // au lieu de patient_id
        'medecin_id',
        'prescripteur_id',
        'examen_id',
        'service_id',
        'assurance_id',
        'date_examen',
        'total',
        'nom_caissier',
        'couverture',
        'numero_facture',
    ];
    public function mode_paiements()
    {
        return $this->hasMany(ModePaiement::class);
    }


    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    public function prescripteur()
    {
        return $this->belongsTo(Prescripteur::class);
    }

    public function examen()
    {
        return $this->belongsTo(Examen::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function assurance()
    {
        return $this->belongsTo(Assurance::class);
    }

    public function patient()
    {
        return $this->belongsTo(GestionPatient::class, 'gestion_patient_id');
    }
    public function paiements()
    {
        return $this->hasOne(ModePaiement::class);
    }
    protected static function booted()
    {
        static::creating(function ($caisse) {
            // Génération du numéro de facture global
            if (empty($caisse->numero_facture)) {
                $max = self::max('numero_facture') ?? 0;
                $caisse->numero_facture = $max + 1;
            }

            // Génération du numéro d'entrée spécifique au médecin ET au jour
            // (partagé entre caisses ET rendez-vous)
            if (!empty($caisse->medecin_id)) {
                $today = now()->startOfDay(); // 00h GMT du jour actuel

                // Compter les caisses de ce médecin aujourd'hui
                $countCaisses = self::where('medecin_id', $caisse->medecin_id)
                    ->whereDate('created_at', $today)
                    ->count();

                // Compter les rendez-vous de ce médecin aujourd'hui
                $countRendezVous = \App\Models\RendezVous::where('medecin_id', $caisse->medecin_id)
                    ->whereDate('created_at', $today)
                    ->count();

                // Total des entrées pour ce médecin aujourd'hui
                $totalEntrees = $countCaisses + $countRendezVous;
                $caisse->numero_entre = $totalEntrees + 1;
            } else {
                // Fallback si pas de médecin (ne devrait pas arriver)
                $caisse->numero_entre = 1;
            }
        });
    }
}

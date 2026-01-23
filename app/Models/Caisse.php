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
        'examens_data' => 'array', // Laravel encode/décode automatiquement
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
        'modified_by',
        'couverture',
        'numero_facture',
        'examens_data',
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
        // Récupérer UNIQUEMENT le paiement initial du patient (caisse_id = this->id)
        // Exclure les parts médecin (source = 'part_medecin') qui ont montant négatif
        return $this->hasOne(ModePaiement::class, 'caisse_id')
                    ->where(function($query) {
                        $query->whereNull('source')
                              ->orWhere('source', '!=', 'part_medecin');
                    })
                    ->where('montant', '>', 0); // Montant positif = paiement du patient
    }

    public function etatCaisse()
    {
        return $this->hasOne(EtatCaisse::class);
    }

    public function modifier()
    {
        return $this->belongsTo(\App\Models\User::class, 'modified_by');
    }

    protected static function booted()
    {
        static::creating(function ($caisse) {
            // Génération du numéro de facture global
            if (empty($caisse->numero_facture)) {
                $max = self::max('numero_facture') ?? 0;
                $caisse->numero_facture = $max + 1;
            }

            // Génération du numéro d'entrée journalier SEULEMENT si pas déjà défini
            if (is_null($caisse->numero_entre) || $caisse->numero_entre === '') {
                // Utiliser la date d'examen si disponible, sinon la date actuelle
                $dateReference = $caisse->date_examen ? \Carbon\Carbon::parse($caisse->date_examen)->startOfDay() : now()->startOfDay();

                // Récupérer tous les numéros d'entrée utilisés pour ce médecin à cette date
                $numerosCaisses = self::where('medecin_id', $caisse->medecin_id)
                    ->whereDate('date_examen', $dateReference)
                    ->pluck('numero_entre')
                    ->toArray();

                $numerosRendezVous = \App\Models\RendezVous::where('medecin_id', $caisse->medecin_id)
                    ->whereDate('date_rdv', $dateReference)
                    ->pluck('numero_entree')
                    ->toArray();

                // Fusionner et trier tous les numéros utilisés
                $numerosUtilises = array_merge($numerosCaisses, $numerosRendezVous);
                sort($numerosUtilises);

                // Trouver le prochain numéro disponible
                $numeroEntree = 1;
                foreach ($numerosUtilises as $numero) {
                    if ($numero >= $numeroEntree) {
                        $numeroEntree = $numero + 1;
                    }
                }

                $caisse->numero_entre = $numeroEntree;
            }
        });
    }
}

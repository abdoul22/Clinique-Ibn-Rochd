<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hospitalisation extends Model
{
    use HasFactory;

    protected $fillable = [
        'gestion_patient_id',
        'medecin_id',
        'service_id',
        'lit_id',
        'date_entree',
        'date_sortie',
        'motif',
        'statut',
        'montant_total',
        'observation',
        // nouveaux champs
        'assurance_id',
        'couverture',
        'admission_at',
        'discharge_at',
        'next_charge_due_at',
        'annulated_by', // utilisateur qui a annulé
    ];

    public function patient()
    {
        return $this->belongsTo(GestionPatient::class, 'gestion_patient_id');
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function lit()
    {
        return $this->belongsTo(Lit::class, 'lit_id');
    }

    public function chambre()
    {
        return $this->hasOneThrough(Chambre::class, Lit::class, 'id', 'id', 'lit_id', 'chambre_id');
    }

    public function roomStays()
    {
        return $this->hasMany(\App\Models\HospitalizationRoomStay::class, 'hospitalisation_id');
    }

    public function annulator()
    {
        return $this->belongsTo(\App\Models\User::class, 'annulated_by');
    }

    public function charges()
    {
        return $this->hasMany(\App\Models\HospitalisationCharge::class, 'hospitalisation_id');
    }

    /**
     * Récupérer tous les médecins impliqués dans cette hospitalisation
     */
    public function getAllInvolvedDoctors()
    {
        $doctors = collect();

        // 1. Médecin traitant principal (seulement s'il existe)
        if ($this->medecin) {
            $doctors->push([
                'medecin' => $this->medecin,
                'role' => 'Médecin Traitant',
                'part_medecin' => 0, // Sera calculé plus tard
                'examens' => []
            ]);
        }

        // 2. Médecins des examens (via les charges)
        $examensCharges = $this->charges()
            ->where('type', 'examen')
            ->whereNotNull('source_id')
            ->with(['hospitalisation'])
            ->get();

        foreach ($examensCharges as $charge) {
            // Récupérer l'examen via source_id
            $examen = \App\Models\Examen::find($charge->source_id);
            if ($examen) {
                // Déterminer le médecin réel de cette charge
                $medecinReel = null;
                $nomExamen = $examen->nom;

                // Si la description contient un nom de médecin entre parenthèses, c'est le médecin réel
                if (preg_match('/\(Dr\.\s+([^)]+)\)/', $charge->description_snapshot, $matches)) {
                    // Chercher le médecin par nom dans la description
                    $nomMedecin = trim($matches[1]);
                    $medecinReel = \App\Models\Medecin::where('nom', 'LIKE', "%{$nomMedecin}%")->first();
                    $nomExamen = str_replace($matches[0], '', $charge->description_snapshot);
                } else {
                    // Utiliser le médecin de l'examen s'il existe
                    $medecinReel = $examen->medecin;
                }

                // Si on a trouvé un médecin, l'ajouter
                if ($medecinReel) {
                    $existingDoctor = $doctors->firstWhere('medecin.id', $medecinReel->id);

                    if ($existingDoctor) {
                        // Ajouter l'examen au médecin existant
                        $existingDoctor['part_medecin'] += $charge->part_medecin;
                        $existingDoctor['examens'][] = [
                            'nom' => trim($nomExamen),
                            'part_medecin' => $charge->part_medecin,
                            'date' => $charge->created_at
                        ];
                    } else {
                        // Nouveau médecin spécialiste
                        $doctors->push([
                            'medecin' => $medecinReel,
                            'role' => 'Médecin Spécialiste',
                            'part_medecin' => $charge->part_medecin,
                            'examens' => [[
                                'nom' => trim($nomExamen),
                                'part_medecin' => $charge->part_medecin,
                                'date' => $charge->created_at
                            ]]
                        ]);
                    }
                }
            }
        }

        return $doctors;
    }

    /**
     * Vérifier si cette hospitalisation a plusieurs médecins
     */
    public function hasMultipleDoctors()
    {
        return $this->getAllInvolvedDoctors()->count() > 1;
    }
}

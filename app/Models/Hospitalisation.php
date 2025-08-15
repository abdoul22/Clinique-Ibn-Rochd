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

    public function charges()
    {
        return $this->hasMany(\App\Models\HospitalisationCharge::class, 'hospitalisation_id');
    }
}

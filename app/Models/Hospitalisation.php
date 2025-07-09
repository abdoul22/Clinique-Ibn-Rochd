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
        'date_entree',
        'date_sortie',
        'motif',
        'statut',
        'chambre',
        'lit',
        'montant_total',
        'observation',
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
        return $this->belongsTo(Lit::class);
    }
}

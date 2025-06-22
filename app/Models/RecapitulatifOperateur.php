<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecapitulatifOperateur extends Model
{
    use HasFactory;

    protected $fillable = [
        'medecin_id',
        'service_id',
        'nombre',
        'tarif',
        'recettes',
        'part_medecin',
        'part_clinique',
        'date',
    ];

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

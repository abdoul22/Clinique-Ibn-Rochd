<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrdonnanceMedicament extends Model
{
    use HasFactory;

    protected $fillable = [
        'ordonnance_id',
        'medicament_id',
        'medicament_nom',
        'dosage',
        'duree',
        'note',
        'ordre',
    ];

    /**
     * Relation avec l'ordonnance
     */
    public function ordonnance()
    {
        return $this->belongsTo(Ordonnance::class);
    }

    /**
     * Relation avec le médicament
     */
    public function medicament()
    {
        return $this->belongsTo(Medicament::class);
    }
}


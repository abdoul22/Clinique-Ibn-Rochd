<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamenAssuranceTarif extends Model
{
    protected $fillable = [
        'examen_id',
        'assurance_id',
        'tarif_assurance',
    ];

    protected $casts = [
        'tarif_assurance' => 'decimal:2',
    ];

    public function examen()
    {
        return $this->belongsTo(Examen::class);
    }

    public function assurance()
    {
        return $this->belongsTo(Assurance::class);
    }
}

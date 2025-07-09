<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lit extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'chambre_id',
        'statut',
    ];

    public function chambre()
    {
        return $this->belongsTo(Chambre::class);
    }

    public function hospitalisations()
    {
        return $this->hasMany(Hospitalisation::class);
    }
}

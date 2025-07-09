<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chambre extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'type',
        'etage',
        'statut',
    ];

    public function lits()
    {
        return $this->hasMany(Lit::class);
    }
}

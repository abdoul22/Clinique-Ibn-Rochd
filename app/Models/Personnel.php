<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'fonction',
        'adresse',
        'telephone',
        'credit',
        'salaire'
    ];

    public function etatsDeCaisseCredit()
    {
        return $this->hasMany(EtatCaisse::class, 'credit_personnel');
    }
}

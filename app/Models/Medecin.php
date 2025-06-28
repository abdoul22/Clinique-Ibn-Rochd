<?php

// app/Models/Medecin.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medecin extends Model
{
    use HasFactory;

    protected $table = 'medecins';

    protected $fillable = [
        'nom',
        'prenom',
        'specialite',
        'telephone',
        'email',
        'statut',
    ];

    public function recapitulatifOperateurs()
    {
        return $this->hasMany(RecapitulatifOperateur::class);
    }
    public function caisses()
    {
        return $this->hasMany(\App\Models\Caisse::class);
    }
    public function examens()
    {
        return $this->hasMany(Examen::class);
    }
    // Medecin.php

    public function etatsCaisse()
    {
        return $this->hasMany(EtatCaisse::class);
    }
}

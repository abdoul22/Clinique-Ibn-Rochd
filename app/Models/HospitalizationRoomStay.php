<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HospitalizationRoomStay extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospitalisation_id',
        'chambre_id',
        'start_at',
        'end_at',
    ];

    public function hospitalisation()
    {
        return $this->belongsTo(Hospitalisation::class);
    }

    public function chambre()
    {
        return $this->belongsTo(Chambre::class);
    }
}

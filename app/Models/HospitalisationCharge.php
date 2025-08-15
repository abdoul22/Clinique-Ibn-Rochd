<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HospitalisationCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospitalisation_id',
        'room_stay_id',
        'type',
        'source_id',
        'description_snapshot',
        'unit_price',
        'quantity',
        'total_price',
        'part_medecin',
        'part_cabinet',
        'is_pharmacy',
        'is_billed',
        'billed_at',
        'caisse_id',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'part_medecin' => 'decimal:2',
        'part_cabinet' => 'decimal:2',
        'is_pharmacy' => 'boolean',
        'is_billed' => 'boolean',
        'billed_at' => 'datetime',
    ];

    public function hospitalisation()
    {
        return $this->belongsTo(Hospitalisation::class);
    }

    public function roomStay()
    {
        return $this->belongsTo(HospitalizationRoomStay::class, 'room_stay_id');
    }
}

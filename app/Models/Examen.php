<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'idsvc', 'tarif',
        'part_cabinet',
        'part_medecin',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'idsvc');
    }
    public function caisse()
    {
        return $this->belongsTo(caisse::class, 'examen_id');
    }

    public static function getTotaux()
    {
        return [
            'part_cabinet_total' => self::sum('part_cabinet'),
            'part_medecin_total' => self::sum('part_medecin'),
            'recettes_total'        => \App\Models\Caisse::sum('total'),
            'depenses_total'        => \App\Models\Depense::sum('montant'),
        ];
    }

    public static function totalPartCabinet()
    {
        return self::sum('part_cabinet');
    }

    public static function totalPartMedecin()
    {
        return self::sum('part_medecin');
    }
}

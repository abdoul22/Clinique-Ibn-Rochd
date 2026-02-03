<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'idsvc',
        'medecin_id',
        'tarif',
        'part_cabinet',
        'part_medecin',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'idsvc');
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    public function caisse()
    {
        return $this->belongsTo(caisse::class, 'examen_id');
    }

    /**
     * Tarifs spécifiques par assurance
     */
    public function assuranceTarifs()
    {
        return $this->hasMany(\App\Models\ExamenAssuranceTarif::class, 'examen_id');
    }

    /**
     * Obtenir le tarif pour une assurance spécifique ou le tarif par défaut
     */
    public function getTarifPourAssurance($assuranceId = null)
    {
        if (!$assuranceId) {
            return $this->tarif;
        }
        
        $tarifAssurance = $this->assuranceTarifs()
            ->where('assurance_id', $assuranceId)
            ->first();
        
        return $tarifAssurance ? $tarifAssurance->tarif_assurance : $this->tarif;
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

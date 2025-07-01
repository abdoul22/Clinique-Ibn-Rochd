<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModePaiement extends Model
{
    protected $fillable = ['caisse_id', 'type', 'montant'];

    public function caisse()
    {
        return $this->belongsTo(Caisse::class);
    }

    /**
     * Récupérer tous les types de modes de paiement disponibles
     */
    public static function getTypes()
    {
        return self::distinct()->pluck('type')->toArray();
    }

    /**
     * Récupérer un mode de paiement par type
     */
    public static function findByType($type)
    {
        return self::where('type', $type)->first();
    }
}

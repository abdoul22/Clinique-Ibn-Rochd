<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModePaiement extends Model
{
    protected $fillable = ['caisse_id', 'type', 'montant', 'source', 'created_at', 'updated_at'];

    public function caisse()
    {
        return $this->belongsTo(Caisse::class);
    }

    public function depense()
    {
        return $this->hasOne(Depense::class, 'mode_paiement_id', 'type')
            ->whereColumn('depenses.created_at', '=', 'mode_paiements.created_at');
    }

    /**
     * Récupérer tous les types de modes de paiement disponibles
     */
    public static function getTypes()
    {
        $types = self::distinct()->pluck('type')->filter()->toArray();
        if (empty($types)) {
            return ['espèces', 'bankily', 'masrivi', 'sedad'];
        }
        // Normaliser/ajouter les manquants si la base est incomplète
        $defaults = collect(['espèces', 'bankily', 'masrivi', 'sedad']);
        return $defaults->merge($types)->unique()->values()->toArray();
    }

    /**
     * Récupérer un mode de paiement par type
     */
    public static function findByType($type)
    {
        return self::where('type', $type)->first();
    }
}

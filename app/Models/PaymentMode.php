<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'display_name', 'is_active'];

    /**
     * Récupérer tous les modes de paiement actifs
     */
    public static function getActiveModes()
    {
        return self::where('is_active', true)->pluck('name')->toArray();
    }

    /**
     * Récupérer tous les modes de paiement avec leurs noms d'affichage
     */
    public static function getActiveModesWithDisplay()
    {
        return self::where('is_active', true)->get();
    }
}

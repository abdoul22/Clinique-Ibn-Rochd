<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{

    protected $table = 'credits'; // 🔁 Assure que le nom de la table est correct
    protected $primaryKey = 'id'; // ✅ Clé primaire utilisée pour route binding

    protected $fillable = [
        'source_type',
        'source_id',
        'montant',
        'montant_paye',
        'status',
        'statut',
        'mode_paiement_id',
        'description',
        'caisse_id',
    ];

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'payé' => 'text-green-600',
            'partiellement payé' => 'text-yellow-500',
            default => 'text-red-500',
        };
    }

    public function source()
    {
        return $this->morphTo();
    }

    // Note: mode_paiement_id est maintenant une chaîne, pas une relation
    public function getModePaiementAttribute()
    {
        return $this->mode_paiement_id;
    }

    public function getNomSourceAttribute()
    {
        return $this->source?->nom ?? '—';
    }

    public function depense()
    {
        return $this->hasOne(\App\Models\Depense::class, 'credit_id');
    }

    public function caisse()
    {
        return $this->belongsTo(\App\Models\Caisse::class);
    }

    public function deduireCredit()
    {
        if (method_exists($this->source, 'updateCredit')) {
            $this->source->updateCredit();
        }
    }
    public static function boot()
    {
        parent::boot();

        static::saving(function ($credit) {
            $credit->statut = match ($credit->status) {
                'payé' => 'Payé',
                'partiellement payé' => 'Partiellement payé',
                default => 'Non payé',
            };
        });
    }
}

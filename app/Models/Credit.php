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
        'statut', // <- tu l'as oublié ici alors qu’il est utilisé dans marquerComme()
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
    public function mode_paiement()
    {
        return $this->belongsTo(ModePaiement::class, 'mode_paiement_id');
    }

    public function getNomSourceAttribute()
    {
        return $this->source?->nom ?? '—';
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



<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    protected $fillable = [
        'type',
        'source_id',
        'montant',
        'montant_paye',
        'status',
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
        return $this->morphTo(null, 'type', 'source_id');
    }

    public function getNomSourceAttribute()
    {
        return $this->source?->nom ?? '—';
    }

    public function deduireCredit()
    {
        if ($this->type === 'App\Models\Personnel') {
            $this->source->decrement('credit', $this->montant);
        } elseif ($this->type === 'App\Models\Assurance') {
            $this->source->decrement('credit', $this->montant);
        }
    }
}



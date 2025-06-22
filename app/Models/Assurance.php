<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assurance extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'credit'];

    public function etatsDeCaisse()
    {
        return $this->hasMany(EtatCaisse::class, 'assurance_id');
    }
    public function getCreditFormatAttribute()
    {
        return number_format($this->credit, 0, ',', ' ') . ' MRU';
    }
}

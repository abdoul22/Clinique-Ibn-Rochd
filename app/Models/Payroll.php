<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'personnel_id',
        'year',
        'month',
        'montant_net',
        'mode',
        'paid_at',
    ];

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }
}

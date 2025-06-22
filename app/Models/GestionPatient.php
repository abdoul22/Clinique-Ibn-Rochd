<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GestionPatient extends Model
{

    use HasFactory;
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',


    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtatCaisse extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'recette',
        'part_medecin',
        'part_clinique',
        'depense',
        'personnel_id',     // lier au personnel
        'assurance_id',
        'caisse_id',
        'medecin_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */


    // app/Models/EtatCaisse.php
    public function medecin()
    {
        return $this->belongsTo(\App\Models\Medecin::class);
    }

    public function examen()
    {
        return $this->belongsTo(Examen::class);
    }

    public function scopeForPersonnel($query)
    {
        return $query->whereNotNull('personnel_id');
    }
    public function scopeGeneral($query)
    {
        return $query->whereNull('personnel_id')->whereNull('assurance_id');
    }
    public function depense()
    {
        return $this->hasOne(Depense::class);
    }

    public function assurance()
    {
        return $this->belongsTo(Assurance::class, 'assurance_id');
    }
    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDesignationAttribute($value)
    {
        if ($this->credit_personnel && $this->personnel) {
            return 'Crédit personnel : ' . $this->personnel->nom;
        }

        if ($this->assurance_id && $this->assurance) {
            return 'Assurance : ' . $this->assurance->nom;
        }

        return $value ?? 'État Général';
    }


    public function getTotalDetteAttribute()
    {
        return ($this->credit ?? 0) + ($this->salaire ?? 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */


    public function scopeForAssurance($query)
    {
        return $query->whereNotNull('assurance_id');
    }



    public function caisse()
    {
        return $this->belongsTo(Caisse::class);
    }
}

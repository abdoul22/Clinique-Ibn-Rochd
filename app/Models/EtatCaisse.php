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
        'validated',
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

    /*
    |--------------------------------------------------------------------------
    | Observers
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        // Créer automatiquement un crédit d'assurance lors de la création d'un état de caisse
        static::created(function ($etatCaisse) {
            // Ne créer le crédit que pour les entrées liées à une facture (caisse_id non null)
            if ($etatCaisse->assurance_id && $etatCaisse->caisse_id) {
                Credit::create([
                    'source_type' => \App\Models\Assurance::class,
                    'source_id' => $etatCaisse->assurance_id,
                    'montant' => $etatCaisse->recette,
                    'montant_paye' => 0,
                    'status' => 'non payé',
                    'statut' => 'Non payé',
                    'caisse_id' => $etatCaisse->caisse_id,
                ]);

                // Mettre à jour le crédit de l'assurance
                $assurance = $etatCaisse->assurance;
                if ($assurance) {
                    $assurance->updateCredit();
                }
            }

            // Validation automatique si la part médecin est 0
            if ($etatCaisse->part_medecin == 0) {
                $etatCaisse->validated = true;
                $etatCaisse->save();
            }
        });
    }
}

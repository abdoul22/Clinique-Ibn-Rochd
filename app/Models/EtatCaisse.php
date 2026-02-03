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
                // Vérifier qu'un crédit n'existe pas déjà pour cette caisse et cette assurance
                $creditExistant = \App\Models\Credit::where('caisse_id', $etatCaisse->caisse_id)
                    ->where('source_type', \App\Models\Assurance::class)
                    ->where('source_id', $etatCaisse->assurance_id)
                    ->first();

                if ($creditExistant) {
                    // Un crédit existe déjà, ne pas en créer un autre
                    return;
                }

                // Calculer le montant dû par l'assurance
                // Le total brut = recette / (1 - couverture/100)
                // Le montant assurance = total_brut * (couverture/100)
                $caisse = $etatCaisse->caisse;
                $couverture = $caisse->couverture ?? 0;
                $recette = $etatCaisse->recette ?? 0;
                
                if ($couverture == 100) {
                    // Si couverture = 100%, l'assurance paie 100% du montant total
                    $montantAssurance = $caisse->total ?? 0;
                } elseif ($couverture > 0 && $couverture < 100) {
                    // Calculer le total brut à partir de la recette et de la couverture
                    $totalBrut = $recette / (1 - ($couverture / 100));
                    $montantAssurance = $totalBrut * ($couverture / 100);
                } else {
                    // Si couverture = 0, pas de crédit d'assurance
                    $montantAssurance = 0;
                }

                if ($montantAssurance > 0) {
                    Credit::create([
                        'source_type' => \App\Models\Assurance::class,
                        'source_id' => $etatCaisse->assurance_id,
                        'montant' => $montantAssurance,
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
            }

            // Validation automatique si la part médecin est 0
            if ($etatCaisse->part_medecin == 0) {
                $etatCaisse->validated = true;
                $etatCaisse->save();
            }
        });
    }
}

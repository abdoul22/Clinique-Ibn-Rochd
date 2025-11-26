<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Ordonnance extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'consultation_id',
        'patient_id',
        'medecin_id',
        'date_ordonnance',
        'date_expiration',
        'notes',
        'statut',
    ];

    protected $casts = [
        'date_ordonnance' => 'date',
        'date_expiration' => 'date',
    ];

    /**
     * Boot method pour générer la référence automatiquement
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ordonnance) {
            if (!$ordonnance->reference) {
                $ordonnance->reference = self::genererReference();
            }
        });
    }

    /**
     * Générer une référence unique
     */
    public static function genererReference()
    {
        $year = Carbon::now()->year;
        $lastOrdonnance = self::whereYear('created_at', $year)->latest()->first();
        $numero = $lastOrdonnance ? (intval(substr($lastOrdonnance->reference, -6)) + 1) : 1;
        
        return 'ORD' . $year . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Relation avec la consultation
     */
    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    /**
     * Relation avec le patient
     */
    public function patient()
    {
        return $this->belongsTo(GestionPatient::class, 'patient_id');
    }

    /**
     * Relation avec le médecin
     */
    public function medecin()
    {
        return $this->belongsTo(Medecin::class, 'medecin_id');
    }

    /**
     * Relation avec les médicaments de l'ordonnance
     */
    public function medicaments()
    {
        return $this->hasMany(OrdonnanceMedicament::class)->orderBy('ordre');
    }

    /**
     * Scope pour les ordonnances actives
     */
    public function scopeActives($query)
    {
        return $query->where('statut', 'active');
    }

    /**
     * Scope pour les ordonnances d'un médecin
     */
    public function scopeParMedecin($query, $medecinId)
    {
        return $query->where('medecin_id', $medecinId);
    }

    /**
     * Vérifier si l'ordonnance est expirée
     */
    public function estExpiree()
    {
        if (!$this->date_expiration) {
            return false;
        }
        return Carbon::now()->gt($this->date_expiration);
    }
}


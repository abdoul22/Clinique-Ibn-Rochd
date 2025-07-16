<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lit extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'chambre_id',
        'statut',
        'type',
        'notes',
    ];

    public function chambre()
    {
        return $this->belongsTo(Chambre::class);
    }

    public function hospitalisations()
    {
        return $this->hasMany(Hospitalisation::class);
    }

    public function hospitalisationActuelle()
    {
        return $this->hasOne(Hospitalisation::class)->where('statut', 'en cours');
    }

    // Méthodes utilitaires
    public function getNomCompletAttribute()
    {
        if ($this->chambre) {
            return $this->chambre->nom . ' - Lit ' . $this->numero;
        }
        return 'Chambre supprimée - Lit ' . $this->numero;
    }

    public function getEstLibreAttribute()
    {
        return $this->statut === 'libre';
    }

    public function getEstOccupeAttribute()
    {
        return $this->statut === 'occupe';
    }

    public function getEstEnMaintenanceAttribute()
    {
        return $this->statut === 'maintenance';
    }

    // Méthodes pour changer le statut
    public function liberer()
    {
        $this->update(['statut' => 'libre']);
    }

    public function occuper()
    {
        $this->update(['statut' => 'occupe']);
    }

    public function mettreEnMaintenance()
    {
        $this->update(['statut' => 'maintenance']);
    }

    public function reserver()
    {
        $this->update(['statut' => 'reserve']);
    }

    // Scopes pour filtrage
    public function scopeLibre($query)
    {
        return $query->where('statut', 'libre');
    }

    public function scopeOccupe($query)
    {
        return $query->where('statut', 'occupe');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('statut', 'maintenance');
    }

    public function scopeParChambre($query, $chambreId)
    {
        return $query->where('chambre_id', $chambreId);
    }
}

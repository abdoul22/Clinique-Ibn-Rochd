<?php
// Service.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'observation',
        'pharmacie_id',
        'type_service', // 'examen', 'medicament', 'consultation'
        'prix',
        'quantite_defaut'
    ];

    public function examens()
    {
        return $this->hasMany(Examen::class, 'idsvc');
    }

    public function recapitulatifJournaliers()
    {
        return $this->hasMany(RecapitulatifServiceJournalier::class, 'idsvc');
    }

    public function recapitulatifOperateurs()
    {
        return $this->hasMany(RecapitulatifOperateur::class);
    }

    /**
     * Relation avec la pharmacie
     */
    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(Pharmacie::class);
    }

    /**
     * Vérifier si le service est lié à un médicament
     */
    public function isMedicament(): bool
    {
        // Nouveau schéma: 'PHARMACIE' stocké en base (ENUM), ancien 'medicament' encore supporté côté vues
        return ($this->type_service === 'PHARMACIE' && $this->pharmacie_id !== null)
            || ($this->type_service === 'medicament' && $this->pharmacie_id !== null);
    }

    /**
     * Vérifier si le service est de type pharmacie
     */
    public function isPharmacie(): bool
    {
        return ($this->type_service === 'PHARMACIE') || ($this->type_service === 'pharmacie');
    }

    /**
     * Obtenir le prix du service (médicament ou service normal)
     */
    public function getPrixServiceAttribute(): float
    {
        if ($this->isMedicament() && $this->pharmacie) {
            return $this->pharmacie->prix_vente;
        }
        return $this->prix ?? 0;
    }

    /**
     * Obtenir la quantité par défaut
     */
    public function getQuantiteDefautAttribute(): int
    {
        if ($this->isMedicament() && $this->pharmacie) {
            return $this->pharmacie->quantite;
        }
        return $this->attributes['quantite_defaut'] ?? 1;
    }
}

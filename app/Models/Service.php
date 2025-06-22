<?php
// Service.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RecapitulatifServiceJournier;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'observation'];

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
}

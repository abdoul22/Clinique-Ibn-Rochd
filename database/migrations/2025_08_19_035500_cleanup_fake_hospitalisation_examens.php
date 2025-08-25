<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Examen;
use App\Models\Service;
use App\Models\Caisse;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Trouver ou créer un examen générique d'hospitalisation
        $serviceHosp = Service::where('type_service', 'HOSPITALISATION')->first();

        if ($serviceHosp) {
            $examenGenerique = Examen::where('idsvc', $serviceHosp->id)
                ->where('nom', 'Hospitalisation')
                ->first();

            if (!$examenGenerique) {
                $examenGenerique = Examen::create([
                    'nom' => 'Hospitalisation',
                    'idsvc' => $serviceHosp->id,
                    'tarif' => 0,
                    'part_cabinet' => 0,
                    'part_medecin' => 0,
                ]);
            }

            // Récupérer tous les faux examens d'hospitalisation
            $fakeExamens = Examen::where('nom', 'LIKE', 'Hospitalisation - %')->get();

            foreach ($fakeExamens as $fakeExamen) {
                // Mettre à jour toutes les références dans la table caisses
                Caisse::where('examen_id', $fakeExamen->id)
                    ->update(['examen_id' => $examenGenerique->id]);

                // Supprimer le faux examen
                $fakeExamen->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas de rollback pour cette migration de nettoyage
    }
};

 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Examen;
use App\Models\Service;
use App\Models\Caisse;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Trouver ou créer un examen générique d'hospitalisation
        $serviceHosp = Service::where('type_service', 'HOSPITALISATION')->first();

        if ($serviceHosp) {
            $examenGenerique = Examen::where('idsvc', $serviceHosp->id)
                ->where('nom', 'Hospitalisation')
                ->first();

            if (!$examenGenerique) {
                $examenGenerique = Examen::create([
                    'nom' => 'Hospitalisation',
                    'idsvc' => $serviceHosp->id,
                    'tarif' => 0,
                    'part_cabinet' => 0,
                    'part_medecin' => 0,
                ]);
            }

            // Récupérer tous les faux examens d'hospitalisation
            $fakeExamens = Examen::where('nom', 'LIKE', 'Hospitalisation - %')->get();

            foreach ($fakeExamens as $fakeExamen) {
                // Mettre à jour toutes les références dans la table caisses
                Caisse::where('examen_id', $fakeExamen->id)
                    ->update(['examen_id' => $examenGenerique->id]);

                // Supprimer le faux examen
                $fakeExamen->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas de rollback pour cette migration de nettoyage
    }
};

 
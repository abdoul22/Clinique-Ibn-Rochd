<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Pharmacie;

class ServicesPharmacieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "=== Création des Services pour tous les Médicaments ===\n";

        // Récupérer tous les médicaments actifs
        $medicaments = Pharmacie::where('statut', 'actif')->get();
        echo "Médicaments trouvés : {$medicaments->count()}\n";

        $servicesCrees = 0;

        foreach ($medicaments as $medicament) {
            // Vérifier si un service existe déjà pour ce médicament
            $serviceExistant = Service::where('pharmacie_id', $medicament->id)
                ->where('type_service', 'medicament')
                ->first();

            if (!$serviceExistant) {
                // Créer un nouveau service
                Service::create([
                    'nom' => "Vente {$medicament->nom_medicament}",
                    'type_service' => 'medicament',
                    'pharmacie_id' => $medicament->id,
                    'prix' => $medicament->prix_vente,
                    'quantite_defaut' => $medicament->quantite,
                    'observation' => "Service de vente pour {$medicament->nom_medicament}",
                ]);

                echo "✓ Service créé pour : {$medicament->nom_medicament}\n";
                $servicesCrees++;
            } else {
                echo "- Service existe déjà pour : {$medicament->nom_medicament}\n";
            }
        }

        echo "\nServices créés : {$servicesCrees}\n";

        // Vérification finale
        $totalServices = Service::where('type_service', 'medicament')->count();
        echo "Total services médicament : {$totalServices}\n";

        echo "=== Terminé ===\n";
    }
}

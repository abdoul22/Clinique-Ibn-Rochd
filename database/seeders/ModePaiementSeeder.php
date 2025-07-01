<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModePaiement;
use App\Models\Caisse;

class ModePaiementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer la première caisse ou en créer une si elle n'existe pas
        $caisse = Caisse::first();

        if (!$caisse) {
            // Créer une caisse avec la structure correcte
            $caisse = Caisse::create([
                'numero_facture' => 1,
                'numero_entre' => 'ENT001',
                'gestion_patient_id' => 1, // Assurez-vous qu'un patient existe
                'medecin_id' => 1, // Assurez-vous qu'un médecin existe
                'examen_id' => 1, // Assurez-vous qu'un examen existe
                'service_id' => 1, // Assurez-vous qu'un service existe
                'date_examen' => now(),
                'total' => 0,
                'nom_caissier' => 'Caissier principal',
            ]);
        }

        $modes = [
            ['type' => 'espèces', 'montant' => 0],
            ['type' => 'bankily', 'montant' => 0],
            ['type' => 'masrivi', 'montant' => 0],
            ['type' => 'sedad', 'montant' => 0],
        ];

        foreach ($modes as $mode) {
            ModePaiement::updateOrCreate(
                ['type' => $mode['type'], 'caisse_id' => $caisse->id],
                ['montant' => $mode['montant']]
            );
        }
    }
}

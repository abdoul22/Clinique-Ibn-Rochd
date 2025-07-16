<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chambre;
use App\Models\Lit;

class ChambreSeeder extends Seeder
{
    public function run()
    {
        // Chambres du Bloc A - Étage 1
        $chambres = [
            [
                'nom' => '101',
                'type' => 'standard',
                'etage' => '1',
                'batiment' => 'Bloc A',
                'capacite_lits' => 2,
                'tarif_journalier' => 15000,
                'description' => 'Chambre standard avec 2 lits',
                'equipements' => 'TV, Climatisation, Salle de bain privée',
            ],
            [
                'nom' => '102',
                'type' => 'standard',
                'etage' => '1',
                'batiment' => 'Bloc A',
                'capacite_lits' => 2,
                'tarif_journalier' => 15000,
                'description' => 'Chambre standard avec 2 lits',
                'equipements' => 'TV, Climatisation, Salle de bain privée',
            ],
            [
                'nom' => '103',
                'type' => 'simple',
                'etage' => '1',
                'batiment' => 'Bloc A',
                'capacite_lits' => 1,
                'tarif_journalier' => 12000,
                'description' => 'Chambre simple avec 1 lit',
                'equipements' => 'Climatisation, Salle de bain privée',
            ],
            [
                'nom' => '104',
                'type' => 'simple',
                'etage' => '1',
                'batiment' => 'Bloc A',
                'capacite_lits' => 1,
                'tarif_journalier' => 12000,
                'description' => 'Chambre simple avec 1 lit',
                'equipements' => 'Climatisation, Salle de bain privée',
            ],

            // Chambres du Bloc A - Étage 2
            [
                'nom' => '201',
                'type' => 'double',
                'etage' => '2',
                'batiment' => 'Bloc A',
                'capacite_lits' => 2,
                'tarif_journalier' => 18000,
                'description' => 'Chambre double avec 2 lits',
                'equipements' => 'TV, Climatisation, Salle de bain privée, Balcon',
            ],
            [
                'nom' => '202',
                'type' => 'double',
                'etage' => '2',
                'batiment' => 'Bloc A',
                'capacite_lits' => 2,
                'tarif_journalier' => 18000,
                'description' => 'Chambre double avec 2 lits',
                'equipements' => 'TV, Climatisation, Salle de bain privée, Balcon',
            ],
            [
                'nom' => '203',
                'type' => 'standard',
                'etage' => '2',
                'batiment' => 'Bloc A',
                'capacite_lits' => 2,
                'tarif_journalier' => 15000,
                'description' => 'Chambre standard avec 2 lits',
                'equipements' => 'TV, Climatisation, Salle de bain privée',
            ],

            // Chambres du Bloc B - Étage 1
            [
                'nom' => '101',
                'type' => 'suite',
                'etage' => '1',
                'batiment' => 'Bloc B',
                'capacite_lits' => 1,
                'tarif_journalier' => 25000,
                'description' => 'Suite de luxe avec 1 lit king-size',
                'equipements' => 'TV 55", Climatisation, Salle de bain privée, Mini-bar, Balcon',
            ],
            [
                'nom' => '102',
                'type' => 'VIP',
                'etage' => '1',
                'batiment' => 'Bloc B',
                'capacite_lits' => 1,
                'tarif_journalier' => 35000,
                'description' => 'Chambre VIP avec 1 lit king-size',
                'equipements' => 'TV 65", Climatisation, Salle de bain privée, Mini-bar, Balcon, Service room',
            ],
            [
                'nom' => '103',
                'type' => 'suite',
                'etage' => '1',
                'batiment' => 'Bloc B',
                'capacite_lits' => 1,
                'tarif_journalier' => 25000,
                'description' => 'Suite de luxe avec 1 lit king-size',
                'equipements' => 'TV 55", Climatisation, Salle de bain privée, Mini-bar, Balcon',
            ],

            // Chambres du Bloc C - Étage 1 (Urgences)
            [
                'nom' => '101',
                'type' => 'standard',
                'etage' => '1',
                'batiment' => 'Bloc C',
                'capacite_lits' => 3,
                'tarif_journalier' => 10000,
                'description' => 'Chambre d\'urgence avec 3 lits',
                'equipements' => 'Climatisation, Salle de bain commune',
            ],
            [
                'nom' => '102',
                'type' => 'standard',
                'etage' => '1',
                'batiment' => 'Bloc C',
                'capacite_lits' => 3,
                'tarif_journalier' => 10000,
                'description' => 'Chambre d\'urgence avec 3 lits',
                'equipements' => 'Climatisation, Salle de bain commune',
            ],
            [
                'nom' => '103',
                'type' => 'standard',
                'etage' => '1',
                'batiment' => 'Bloc C',
                'capacite_lits' => 2,
                'tarif_journalier' => 12000,
                'description' => 'Chambre d\'urgence avec 2 lits',
                'equipements' => 'Climatisation, Salle de bain commune',
            ],
        ];

        foreach ($chambres as $chambreData) {
            $chambre = Chambre::create($chambreData);

            // Créer les lits pour chaque chambre
            for ($i = 1; $i <= $chambre->capacite_lits; $i++) {
                Lit::create([
                    'numero' => $i,
                    'chambre_id' => $chambre->id,
                    'statut' => 'libre',
                    'type' => 'standard',
                    'notes' => null,
                ]);
            }
        }

        $this->command->info('Chambres et lits créés avec succès !');
    }
}

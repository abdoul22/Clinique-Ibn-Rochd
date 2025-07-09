<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pharmacie;

class PharmacieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicaments = [
            [
                'nom_medicament' => 'Paracétamol 500mg',
                'prix_achat' => 50,
                'prix_vente' => 100,
                'prix_unitaire' => 100,
                'quantite' => 20,
                'stock' => 500,
                'description' => 'Antalgique et antipyrétique',
                'categorie' => 'Antalgiques',
                'fournisseur' => 'Pharmacie Centrale',
                'date_expiration' => '2026-12-31',
                'statut' => 'actif'
            ],
            [
                'nom_medicament' => 'Ibuprofène 400mg',
                'prix_achat' => 60,
                'prix_vente' => 120,
                'prix_unitaire' => 120,
                'quantite' => 30,
                'stock' => 300,
                'description' => 'Anti-inflammatoire non stéroïdien',
                'categorie' => 'Anti-inflammatoires',
                'fournisseur' => 'Pharmacie Centrale',
                'date_expiration' => '2026-10-15',
                'statut' => 'actif'
            ],
            [
                'nom_medicament' => 'Amoxicilline 500mg',
                'prix_achat' => 150,
                'prix_vente' => 300,
                'prix_unitaire' => 300,
                'quantite' => 12,
                'stock' => 200,
                'description' => 'Antibiotique à large spectre',
                'categorie' => 'Antibiotiques',
                'fournisseur' => 'Laboratoires Merck',
                'date_expiration' => '2026-08-20',
                'statut' => 'actif'
            ],
            [
                'nom_medicament' => 'Oméprazole 20mg',
                'prix_achat' => 200,
                'prix_vente' => 400,
                'prix_unitaire' => 400,
                'quantite' => 14,
                'stock' => 150,
                'description' => 'Inhibiteur de la pompe à protons',
                'categorie' => 'Anti-ulcéreux',
                'fournisseur' => 'Laboratoires AstraZeneca',
                'date_expiration' => '2026-11-30',
                'statut' => 'actif'
            ],
            [
                'nom_medicament' => 'Vitamine C 500mg',
                'prix_achat' => 30,
                'prix_vente' => 80,
                'prix_unitaire' => 80,
                'quantite' => 60,
                'stock' => 1000,
                'description' => 'Complément alimentaire',
                'categorie' => 'Vitamines',
                'fournisseur' => 'Pharmacie Centrale',
                'date_expiration' => '2027-03-15',
                'statut' => 'actif'
            ],
            [
                'nom_medicament' => 'Doliprane 1000mg',
                'prix_achat' => 80,
                'prix_vente' => 150,
                'prix_unitaire' => 150,
                'quantite' => 8,
                'stock' => 0,
                'description' => 'Antalgique et antipyrétique',
                'categorie' => 'Antalgiques',
                'fournisseur' => 'Laboratoires Sanofi',
                'date_expiration' => '2026-09-10',
                'statut' => 'rupture'
            ],
            [
                'nom_medicament' => 'Aspirine 500mg',
                'prix_achat' => 40,
                'prix_vente' => 90,
                'prix_unitaire' => 90,
                'quantite' => 24,
                'stock' => 50,
                'description' => 'Anti-inflammatoire et antalgique',
                'categorie' => 'Antalgiques',
                'fournisseur' => 'Pharmacie Centrale',
                'date_expiration' => '2026-07-25',
                'statut' => 'actif'
            ],
            [
                'nom_medicament' => 'Zinc 15mg',
                'prix_achat' => 25,
                'prix_vente' => 70,
                'prix_unitaire' => 70,
                'quantite' => 30,
                'stock' => 800,
                'description' => 'Oligo-élément essentiel',
                'categorie' => 'Oligo-éléments',
                'fournisseur' => 'Pharmacie Centrale',
                'date_expiration' => '2027-01-20',
                'statut' => 'actif'
            ]
        ];

        foreach ($medicaments as $medicament) {
            Pharmacie::create($medicament);
        }
    }
}

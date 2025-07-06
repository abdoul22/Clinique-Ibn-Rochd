<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Motif;

class MotifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $motifs = [
            [
                'nom' => 'Consultation générale',
                'description' => 'Consultation de routine pour un examen général',
                'actif' => true
            ],
            [
                'nom' => 'Suivi traitement',
                'description' => 'Suivi d\'un traitement en cours',
                'actif' => true
            ],
            [
                'nom' => 'Consultation d\'urgence',
                'description' => 'Consultation pour un problème urgent',
                'actif' => true
            ],
            [
                'nom' => 'Bilan de santé',
                'description' => 'Bilan complet de l\'état de santé',
                'actif' => true
            ],
            [
                'nom' => 'Prescription médicale',
                'description' => 'Renouvellement ou nouvelle prescription',
                'actif' => true
            ],
            [
                'nom' => 'Analyse de résultats',
                'description' => 'Discussion des résultats d\'analyses',
                'actif' => true
            ],
            [
                'nom' => 'Consultation spécialisée',
                'description' => 'Consultation avec un spécialiste',
                'actif' => true
            ],
            [
                'nom' => 'Vaccination',
                'description' => 'Consultation pour vaccination',
                'actif' => true
            ],
            [
                'nom' => 'Certificat médical',
                'description' => 'Délivrance d\'un certificat médical',
                'actif' => true
            ],
            [
                'nom' => 'Conseil médical',
                'description' => 'Conseil et orientation médicale',
                'actif' => true
            ]
        ];

        foreach ($motifs as $motif) {
            Motif::create($motif);
        }
    }
}

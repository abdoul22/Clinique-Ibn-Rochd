<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicament;

class MedicamentsSeeder extends Seeder
{
    public function run(): void
    {
        $medicaments = [
            // Sirops
            ['nom' => 'ZADITEN', 'forme' => 'sirop', 'dosage' => '5ml', 'actif' => true],
            ['nom' => 'CLARUIS', 'forme' => 'sirop', 'dosage' => '5ml', 'actif' => true],
            ['nom' => 'ANVIR', 'forme' => 'sirop', 'dosage' => '5ml', 'actif' => true],
            ['nom' => 'PRIMALAN', 'forme' => 'sirop', 'dosage' => '5ml', 'actif' => true],
            ['nom' => 'AMOXIL', 'forme' => 'sirop', 'dosage' => '250mg', 'actif' => true],
            
            // Gouttes
            ['nom' => 'OMAT', 'forme' => 'ORAT BAILLY', 'dosage' => null, 'actif' => true],
            ['nom' => 'LOXAPAC', 'forme' => 'GOUTTE', 'dosage' => null, 'actif' => true],
            ['nom' => 'LETTRAZON', 'forme' => 'BPBX', 'dosage' => null, 'actif' => true],
            
            // Comprimés courants
            ['nom' => 'PERFALGAN', 'forme' => 'comprimé', 'dosage' => '400MG', 'actif' => true],
            ['nom' => 'NOVALGIN', 'forme' => 'INJ 2CC', 'dosage' => null, 'actif' => true],
            ['nom' => 'CLAFORAN', 'forme' => 'comprimé', 'dosage' => '400mg', 'actif' => true],
            ['nom' => 'NFS CRP', 'forme' => 'GE', 'dosage' => null, 'actif' => true],
            ['nom' => 'SEROLOGIE', 'forme' => 'DINGUE', 'dosage' => null, 'actif' => true],
            
            // Antibiotiques
            ['nom' => 'AUGMENTIN', 'forme' => 'comprimé', 'dosage' => '1g', 'actif' => true],
            ['nom' => 'AMOXICILLINE', 'forme' => 'comprimé', 'dosage' => '500mg', 'actif' => true],
            ['nom' => 'AZITHROMYCINE', 'forme' => 'comprimé', 'dosage' => '250mg', 'actif' => true],
            
            // Anti-inflammatoires
            ['nom' => 'IBUPROFÈNE', 'forme' => 'comprimé', 'dosage' => '400mg', 'actif' => true],
            ['nom' => 'DICLOFÉNAC', 'forme' => 'comprimé', 'dosage' => '50mg', 'actif' => true],
            
            // Antalgiques
            ['nom' => 'PARACÉTAMOL', 'forme' => 'comprimé', 'dosage' => '500mg', 'actif' => true],
            ['nom' => 'DOLIPRANE', 'forme' => 'comprimé', 'dosage' => '1000mg', 'actif' => true],
            
            // Antihistaminiques
            ['nom' => 'CÉTIRIZINE', 'forme' => 'comprimé', 'dosage' => '10mg', 'actif' => true],
            ['nom' => 'LORATADINE', 'forme' => 'comprimé', 'dosage' => '10mg', 'actif' => true],
        ];

        foreach ($medicaments as $medicament) {
            Medicament::create($medicament);
        }
    }
}


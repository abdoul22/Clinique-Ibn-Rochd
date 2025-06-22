<?php

namespace Database\Seeders;

use App\Models\Caisse;
use Illuminate\Database\Seeder;

class CaisseSeeder extends Seeder
{
    public function run(): void
    {
        // Génère 20 caisses avec numéro_facture auto-incrémenté via la factory
        Caisse::factory()->count(20)->create();
    }
}

<?php

namespace Database\Seeders;
// database/seeders/EtatCaisseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EtatCaisse;

class EtatCaisseSeeder extends Seeder
{
public function run(): void
{
EtatCaisse::factory()->count(19)->create();
}
}

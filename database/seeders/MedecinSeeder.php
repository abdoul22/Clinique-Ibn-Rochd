<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medecin;

class MedecinSeeder extends Seeder
{
    public function run(): void
    {
        Medecin::factory()->count(15)->create();
    }
}

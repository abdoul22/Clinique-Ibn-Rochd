<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GestionPatient;

class GestionPatientSeeder extends Seeder
{
    public function run(): void
    {
        GestionPatient::factory()->count(20)->create();
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prescripteur;

class PrescripteurSeeder extends Seeder
{
    public function run(): void
    {
        Prescripteur::factory()->count(15)->create();
    }
}

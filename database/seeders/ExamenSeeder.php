<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Examen;

class ExamenSeeder extends Seeder
{
    public function run(): void
    {
        Examen::factory()->count(20)->create();
    }
}

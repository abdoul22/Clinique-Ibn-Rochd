<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Depense;

class DepenseSeeder extends Seeder
{
    public function run(): void
    {
        Depense::factory()->count(10)->create();
    }
}

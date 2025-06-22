<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assurance;

class AssuranceSeeder extends Seeder
{
    public function run(): void
    {
        Assurance::factory()->count(10)->create();
    }
}

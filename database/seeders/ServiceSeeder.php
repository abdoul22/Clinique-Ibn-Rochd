<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = ['Radiologie', 'Laboratoire', 'Consultation', 'Urgence'];

        foreach ($services as $service) {
            Service::create([
                'nom' => $service,
                'observation' => fake()->sentence()
            ]);
        }
    }
}

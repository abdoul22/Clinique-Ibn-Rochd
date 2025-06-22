<?php

namespace Database\Factories;
// database/factories/ServiceFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom' => $this->faker->unique()->words(2, true), // exemple : "Imagerie Médicale"
            'observation' => $this->faker->optional()->sentence(6), // ou null
        ];
    }
}

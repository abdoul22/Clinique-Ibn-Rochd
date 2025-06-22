<?php

// database/factories/AssuranceFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AssuranceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom' => $this->faker->company . ' Assurance',
            'credit' => $this->faker->numberBetween(1000, 50000),
        ];
    }
}

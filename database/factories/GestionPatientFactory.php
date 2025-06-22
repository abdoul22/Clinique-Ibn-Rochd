<?php

namespace Database\Factories;
// database/factories/GestionPatientFactory.php

use Illuminate\Database\Eloquent\Factories\Factory;

class GestionPatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name'     => $this->faker->firstName,
            'last_name'      => $this->faker->lastName,
            'date_of_birth'  => $this->faker->date('Y-m-d', '-18 years'), // Âge minimum 18 ans
            'gender'         => $this->faker->randomElement(['HOMME', 'FEMME']),
            'phone'          => $this->faker->phoneNumber,
        ];
    }
}

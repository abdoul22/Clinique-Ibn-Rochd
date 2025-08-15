<?php

/// database/factories/MedecinFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MedecinFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'specialite' => $this->faker->randomElement([
                'Cardiologie',
                'Dermatologie',
                'Chirurgie',
                'Pédiatrie',
                'Radiologie',
                'ORL'
            ]),
            'telephone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'statut' => $this->faker->randomElement(['actif', 'inactif']),
        ];
    }
}

<?php

// database/factories/PersonnelFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PersonnelFactory extends Factory
{
    public function definition(): array
    {
        $salaire = $this->faker->numberBetween(50000, 150000);
        $credit = $this->faker->numberBetween(0, $salaire); // jamais supérieur au salaire

        return [
            'nom'       => $this->faker->name,
            'fonction'  => $this->faker->jobTitle,
            'adresse'   => $this->faker->address,
            'telephone' => $this->faker->phoneNumber,
            'salaire'   => $salaire,
            'credit'    => $credit,
        ];
    }
}

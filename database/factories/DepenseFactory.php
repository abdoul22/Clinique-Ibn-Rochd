<?php

namespace Database\Factories;
use App\Models\Depense;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepenseFactory extends Factory
{
protected $model = Depense::class;

public function definition(): array
{
return [
'nom' => $this->faker->sentence(3),
'montant' => $this->faker->randomFloat(2, 100, 1000),
];
}
}

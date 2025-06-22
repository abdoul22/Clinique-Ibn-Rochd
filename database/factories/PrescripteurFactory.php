<?php

namespace Database\Factories;
use App\Models\Prescripteur;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrescripteurFactory extends Factory
{
protected $model = Prescripteur::class;

public function definition(): array
{
return [
'nom' => $this->faker->name,
'specialite' => $this->faker->word,
];
}
}

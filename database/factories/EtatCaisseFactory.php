<?php

namespace Database\Factories;
use App\Models\EtatCaisse;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Personnel;
use App\Models\Assurance;
use App\Models\Caisse;

class EtatCaisseFactory extends Factory
{
protected $model = EtatCaisse::class;

public function definition(): array
{
return [
'designation' => 'Facture caisse n°' . $this->faker->unique()->numberBetween(1, 1000),
'recette' => $this->faker->randomFloat(2, 500, 5000),
'part_medecin' => $this->faker->randomFloat(2, 100, 2000),
'part_clinique' => $this->faker->randomFloat(2, 50, 500),
'depense' => $this->faker->randomFloat(2, 100, 1000),
'personnel_id' => Personnel::factory(),
'assurance_id' => Assurance::factory(),
'caisse_id' => Caisse::factory(),
];
}
}

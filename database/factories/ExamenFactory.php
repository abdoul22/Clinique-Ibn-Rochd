<?php

namespace Database\Factories;
// database/factories/ExamenFactory.php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamenFactory extends Factory
{
    public function definition(): array
    {
        // tarif total aléatoire
        $tarif = $this->faker->numberBetween(500, 10000);

        // partages logiques
        $part_cabinet = round($tarif * 0.3, 2); // 30%
        $part_medecin = round($tarif - $part_cabinet, 2); // le reste

        return [
            'nom' => 'Examen ' . $this->faker->word,
            'idsvc' => Service::inRandomOrder()->value('id') ?? Service::factory(), // lien avec un service existant ou nouveau
            'tarif' => $tarif,
            'part_cabinet' => $part_cabinet,
            'part_medecin' => $part_medecin,
        ];
    }
}

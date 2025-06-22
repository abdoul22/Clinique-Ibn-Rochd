<?php

namespace Database\Factories;

use App\Models\Caisse;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\GestionPatient;
use App\Models\Medecin;
use App\Models\Prescripteur;
use App\Models\Examen;
use App\Models\Service;


class CaisseFactory extends Factory
{
    protected $model = Caisse::class;
    static $factureNumber = null;
    public function definition(): array
    {

        if (is_null(self::$factureNumber)) {
            self::$factureNumber = Caisse::max('numero_facture') ?? 0;
        }

        return [
            'numero_facture' => ++self::$factureNumber,
            'numero_entre' => $this->faker->unique()->numberBetween(1000, 9999),
            'gestion_patient_id' => GestionPatient::factory(),
            'medecin_id' => Medecin::factory(),
            'prescripteur_id' => Prescripteur::factory(),
            'examen_id' => Examen::factory(),
            'service_id' => Service::factory(),
            'date_examen' => $this->faker->date(),
            'total' => $this->faker->randomFloat(2, 100, 1000),
            'nom_caissier' => 'Super Admin',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\RendezVous;
use App\Models\Medecin;
use App\Models\GestionPatient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RendezVous>
 */
class RendezVousFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $motifs = [
            'Consultation de routine',
            'Suivi traitement',
            'Examen médical',
            'Consultation spécialisée',
            'Contrôle post-opératoire',
            'Vaccination',
            'Analyse de sang',
            'Radiographie',
            'Échographie',
            'Consultation d\'urgence'
        ];

        $statuts = ['en_attente', 'confirme', 'annule', 'termine'];
        $durees = [15, 30, 45, 60, 90];

        return [
            'patient_id' => GestionPatient::factory(),
            'medecin_id' => Medecin::factory(),
            'date_rdv' => $this->faker->dateTimeBetween('now', '+30 days'),
            'heure_rdv' => $this->faker->time('H:i:s'),
            'motif' => $this->faker->randomElement($motifs),
            'statut' => $this->faker->randomElement($statuts),
            'notes' => $this->faker->optional(0.3)->sentence(),
            'duree_consultation' => $this->faker->randomElement($durees),
        ];
    }

    /**
     * Indicate that the appointment is confirmed.
     */
    public function confirme(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut' => 'confirme',
        ]);
    }

    /**
     * Indicate that the appointment is cancelled.
     */
    public function annule(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut' => 'annule',
        ]);
    }

    /**
     * Indicate that the appointment is completed.
     */
    public function termine(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut' => 'termine',
        ]);
    }

    /**
     * Indicate that the appointment is pending.
     */
    public function enAttente(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut' => 'en_attente',
        ]);
    }

    /**
     * Create an appointment for today.
     */
    public function aujourdhui(): static
    {
        return $this->state(fn(array $attributes) => [
            'date_rdv' => Carbon::today(),
        ]);
    }

    /**
     * Create an appointment for tomorrow.
     */
    public function demain(): static
    {
        return $this->state(fn(array $attributes) => [
            'date_rdv' => Carbon::tomorrow(),
        ]);
    }
}

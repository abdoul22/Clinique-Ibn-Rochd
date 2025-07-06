<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RendezVous;
use App\Models\Medecin;
use App\Models\GestionPatient;
use Carbon\Carbon;

class RendezVousSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medecins = Medecin::all();
        $patients = GestionPatient::all();

        if ($medecins->isEmpty() || $patients->isEmpty()) {
            $this->command->warn('Aucun médecin ou patient trouvé. Créez d\'abord des médecins et des patients.');
            return;
        }

        $statuts = ['en_attente', 'confirme', 'annule', 'termine'];
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

        // Créer des rendez-vous pour les 30 prochains jours
        for ($i = 0; $i < 50; $i++) {
            $date = Carbon::now()->addDays(rand(0, 30));
            $heure = Carbon::createFromTime(rand(8, 17), rand(0, 3) * 15, 0);

            RendezVous::create([
                'patient_id' => $patients->random()->id,
                'medecin_id' => $medecins->random()->id,
                'date_rdv' => $date->format('Y-m-d'),
                'heure_rdv' => $heure->format('H:i:s'),
                'motif' => $motifs[array_rand($motifs)],
                'statut' => $statuts[array_rand($statuts)],
                'notes' => rand(0, 1) ? 'Notes supplémentaires pour ce rendez-vous.' : null,
                'duree_consultation' => [15, 30, 45, 60, 90][array_rand([15, 30, 45, 60, 90])],
            ]);
        }

        $this->command->info('50 rendez-vous de test ont été créés.');
    }
}

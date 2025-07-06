<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DossierMedical;
use App\Models\GestionPatient;
use App\Models\Caisse;
use App\Http\Controllers\DossierMedicalController;
use Carbon\Carbon;

class DossierMedicalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer tous les patients qui ont des examens
        $patientsAvecExamens = GestionPatient::whereHas('caisses')->get();

        foreach ($patientsAvecExamens as $patient) {
            // Créer ou mettre à jour le dossier médical
            DossierMedicalController::creerOuMettreAJour($patient->id);
        }

        $this->command->info('Dossiers médicaux créés avec succès pour ' . $patientsAvecExamens->count() . ' patients.');
    }
}

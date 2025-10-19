<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GestionPatient;
use Carbon\Carbon;

class UpdatePatientAges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patients:update-ages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Met à jour l\'âge des patients existants à partir de leur date de naissance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mise à jour des âges des patients...');

        $patients = GestionPatient::whereNotNull('date_of_birth')->get();
        $updated = 0;

        foreach ($patients as $patient) {
            if ($patient->date_of_birth) {
                $age = Carbon::parse($patient->date_of_birth)->age;
                $patient->age = $age;
                $patient->save();
                $updated++;

                $this->line("Patient {$patient->first_name} {$patient->last_name}: {$age} ans");
            }
        }

        $this->info("✅ {$updated} patients mis à jour avec succès !");

        return 0;
    }
}

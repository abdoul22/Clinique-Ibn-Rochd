<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UsersTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesTableSeeder::class,
            UsersTableSeeder::class,  // 2. Crée les utilisateurs

            // Ajoutez d'autres seeders ici
            AssuranceSeeder::class,
            PersonnelSeeder::class,
            MedecinSeeder::class,
            PrescripteurSeeder::class,
            ServiceSeeder::class,
            GestionPatientSeeder::class,
            ExamenSeeder::class,
            DepenseSeeder::class,
            CaisseSeeder::class,
            EtatCaisseSeeder::class,
            ModePaiementSeeder::class,
            MotifSeeder::class,
            RendezVousSeeder::class,

        ]);

        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}

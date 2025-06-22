<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder

{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Création du Super Admin
        $superAdminRole = Role::where('name', 'super_admin')->first();

        User::firstOrCreate(
            ['email' => 'superadmin@clinique.fr'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('MotDePasseComplexe'),
                'role_id' => $superAdminRole->id
            ]
        );

        // 2. Création d'un Admin classique
        $adminRole = Role::where('name', 'admin')->first();

        User::firstOrCreate(
            ['email' => 'admin@clinique.fr'],
            [
                'name' => 'Admin',
                'password' => Hash::make('AutreMotDePasse'),
                'role_id' => $adminRole->id
            ]
        );
    }
}

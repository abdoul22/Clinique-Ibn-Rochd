<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier l'ENUM de la colonne fonction pour ajouter 'Phr'
        DB::statement("ALTER TABLE medecins MODIFY COLUMN fonction ENUM('Pr','Dr','Tss','SGF','IDE','Phr') NOT NULL DEFAULT 'Dr'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retirer 'Phr' de l'ENUM (attention: cela peut causer des erreurs si des médecins ont 'Phr')
        DB::statement("ALTER TABLE medecins MODIFY COLUMN fonction ENUM('Pr','Dr','Tss','SGF','IDE') NOT NULL DEFAULT 'Dr'");
    }
};

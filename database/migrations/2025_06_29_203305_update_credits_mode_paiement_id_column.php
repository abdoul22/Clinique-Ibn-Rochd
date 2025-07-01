<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère existante
            $table->dropForeign(['mode_paiement_id']);

            // Modifier la colonne pour accepter des chaînes
            $table->string('mode_paiement_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            // Remettre la contrainte de clé étrangère
            $table->foreignId('mode_paiement_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};

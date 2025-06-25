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
        Schema::create('etat_caisses', function (Blueprint $table) {
            $table->id();
            $table->string('designation');
            $table->decimal('recette', 10, 2)->default(0);
            $table->decimal('part_medecin', 10, 2)->default(0);
            $table->decimal('part_clinique', 10, 2)->default(0);
            $table->decimal('depense', 10, 2)->default(0);
            $table->foreignId('personnel_id')->nullable()->constrained('personnels')->onDelete('set null'); // ✅ lien vers la personne
            $table->foreignId('assurance_id')->nullable()->constrained('assurances')->onDelete('set null');
            $table->foreignId('caisse_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('medecin_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('validated')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etat_caisses');
    }
};

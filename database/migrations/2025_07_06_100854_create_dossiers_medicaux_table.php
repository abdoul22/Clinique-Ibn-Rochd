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
        Schema::create('dossiers_medicaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('gestion_patients')->onDelete('cascade');
            $table->string('numero_dossier')->unique();
            $table->date('date_creation');
            $table->date('derniere_visite')->nullable();
            $table->integer('nombre_visites')->default(0);
            $table->decimal('total_depense', 10, 2)->default(0);
            $table->enum('statut', ['actif', 'inactif', 'archive'])->default('actif');
            $table->text('notes_generales')->nullable();
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['patient_id', 'statut']);
            $table->index(['derniere_visite']);
            $table->index(['numero_dossier']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossiers_medicaux');
    }
};

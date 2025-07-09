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
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('gestion_patients')->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained('medecins')->onDelete('cascade');
            $table->date('date_rdv');
            $table->time('heure_rdv')->nullable();
            $table->string('motif');
            $table->enum('statut', ['confirme', 'annule'])->default('confirme');
            $table->integer('numero_entree');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->integer('duree_consultation')->default(30); // en minutes
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['date_rdv', 'medecin_id']);
            $table->index(['patient_id', 'date_rdv']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};

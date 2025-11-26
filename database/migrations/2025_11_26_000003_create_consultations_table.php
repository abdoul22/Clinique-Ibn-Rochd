<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('medecin_id');
            $table->unsignedBigInteger('dossier_medical_id')->nullable();
            $table->date('date_consultation');
            $table->time('heure_consultation')->nullable();
            
            // Champs du rapport médical
            $table->string('motif')->nullable();
            $table->text('antecedents')->nullable();
            $table->text('ras')->nullable(); // Rien À Signaler
            $table->text('histoire_maladie')->nullable();
            $table->text('examen_clinique')->nullable();
            $table->text('conduite_tenir')->nullable();
            $table->text('resume')->nullable();
            
            // Métadonnées
            $table->enum('statut', ['en_cours', 'terminee', 'annulee'])->default('en_cours');
            $table->timestamps();
            
            // Clés étrangères
            $table->foreign('patient_id')->references('id')->on('gestion_patients')->onDelete('cascade');
            $table->foreign('medecin_id')->references('id')->on('medecins')->onDelete('cascade');
            $table->foreign('dossier_medical_id')->references('id')->on('dossiers_medicaux')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};


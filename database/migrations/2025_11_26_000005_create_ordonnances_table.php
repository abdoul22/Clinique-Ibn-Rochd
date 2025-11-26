<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordonnances', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // Ex: ORD-2025-001234
            $table->unsignedBigInteger('consultation_id')->nullable();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('medecin_id');
            $table->date('date_ordonnance');
            $table->date('date_expiration')->nullable();
            $table->text('notes')->nullable();
            $table->enum('statut', ['active', 'expiree', 'annulee'])->default('active');
            $table->timestamps();
            
            // Clés étrangères
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('set null');
            $table->foreign('patient_id')->references('id')->on('gestion_patients')->onDelete('cascade');
            $table->foreign('medecin_id')->references('id')->on('medecins')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordonnances');
    }
};


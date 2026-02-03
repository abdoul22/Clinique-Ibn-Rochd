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
        Schema::create('examen_assurance_tarifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examen_id')->constrained('examens')->onDelete('cascade');
            $table->foreignId('assurance_id')->constrained('assurances')->onDelete('cascade');
            $table->decimal('tarif_assurance', 10, 2);
            $table->timestamps();
            
            // Un examen ne peut avoir qu'un seul tarif par assurance
            $table->unique(['examen_id', 'assurance_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examen_assurance_tarifs');
    }
};

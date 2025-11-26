<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordonnance_medicaments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ordonnance_id');
            $table->unsignedBigInteger('medicament_id')->nullable();
            $table->string('medicament_nom'); // Nom du médicament (peut être custom si medicament_id est null)
            $table->string('dosage')->nullable(); // Ex: 5ml 3fois par jour, 1cp le soir
            $table->string('duree')->nullable(); // Ex: 10jours, 2 semaines
            $table->text('note')->nullable();
            $table->integer('ordre')->default(1); // Pour l'ordre d'affichage
            $table->timestamps();
            
            // Clés étrangères
            $table->foreign('ordonnance_id')->references('id')->on('ordonnances')->onDelete('cascade');
            $table->foreign('medicament_id')->references('id')->on('medicaments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordonnance_medicaments');
    }
};


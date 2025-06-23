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
        Schema::create('caisses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('numero_facture')->unique();
            $table->string('numero_entre');
            $table->unsignedBigInteger('gestion_patient_id');
            $table->unsignedBigInteger('medecin_id');
            $table->unsignedBigInteger('prescripteur_id')->nullable();
            $table->unsignedBigInteger('examen_id');
            $table->unsignedBigInteger('service_id');
            $table->date('date_examen');
            $table->decimal('total', 10, 2);
            $table->string('nom_caissier');
           // $table->unsignedBigInteger('assurance_id')->nullable();
            $table->unsignedBigInteger('assurance_id')->nullable();
            $table->integer('couverture')->nullable(); // Exemple : 70, 100
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caisses');
    }
};

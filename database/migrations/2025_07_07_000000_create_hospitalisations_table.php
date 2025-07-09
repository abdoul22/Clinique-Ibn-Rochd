<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hospitalisations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gestion_patient_id');
            $table->unsignedBigInteger('medecin_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->date('date_entree');
            $table->date('date_sortie')->nullable();
            $table->string('motif')->nullable();
            $table->string('statut')->default('en cours'); // en cours, terminé, annulé
            $table->string('chambre')->nullable();
            $table->string('lit')->nullable();
            $table->decimal('montant_total', 10, 2)->nullable();
            $table->text('observation')->nullable();
            $table->timestamps();

            $table->foreign('gestion_patient_id')->references('id')->on('gestion_patients')->onDelete('cascade');
            $table->foreign('medecin_id')->references('id')->on('medecins')->onDelete('set null');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospitalisations');
    }
};

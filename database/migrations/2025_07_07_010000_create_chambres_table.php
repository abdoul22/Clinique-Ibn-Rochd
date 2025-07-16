<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chambres', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // ex: 101, 102, Suite A
            $table->string('type')->default('standard'); // standard, simple, double, suite, VIP
            $table->string('etage')->nullable();
            $table->string('batiment')->nullable(); // Bloc A, B, C
            $table->string('statut')->default('active'); // active, inactive, maintenance
            $table->integer('capacite_lits')->default(1); // nombre de lits dans la chambre
            $table->decimal('tarif_journalier', 10, 2)->nullable(); // tarif par jour
            $table->text('description')->nullable();
            $table->text('equipements')->nullable(); // TV, climatisation, etc.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chambres');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lits', function (Blueprint $table) {
            $table->id();
            $table->string('numero'); // ex: 1, 2, 3, A1, B2
            $table->unsignedBigInteger('chambre_id');
            $table->string('statut')->default('libre'); // libre, occupe, maintenance, reserve
            $table->string('type')->default('standard'); // standard, electrique, manuel
            $table->text('notes')->nullable(); // notes spéciales sur le lit
            $table->timestamps();

            $table->foreign('chambre_id')->references('id')->on('chambres')->onDelete('cascade');
            $table->unique(['numero', 'chambre_id']); // un numéro unique par chambre
        });
    }

    public function down()
    {
        Schema::dropIfExists('lits');
    }
};

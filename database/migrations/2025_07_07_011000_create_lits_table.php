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
            $table->string('statut')->default('libre'); // libre, occupe, maintenance
            $table->timestamps();

            $table->foreign('chambre_id')->references('id')->on('chambres')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lits');
    }
};

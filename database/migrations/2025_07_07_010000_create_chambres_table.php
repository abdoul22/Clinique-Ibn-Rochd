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
            $table->string('nom'); // ex: Bloc A, Suite 101
            $table->string('type')->nullable(); // simple, double, suite, etc.
            $table->string('etage')->nullable();
            $table->string('statut')->default('active'); // active, inactive
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chambres');
    }
};

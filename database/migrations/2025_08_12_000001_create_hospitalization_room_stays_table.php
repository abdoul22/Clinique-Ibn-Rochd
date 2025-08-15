<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospitalization_room_stays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospitalisation_id');
            $table->unsignedBigInteger('chambre_id');
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->timestamps();

            $table->foreign('hospitalisation_id')->references('id')->on('hospitalisations')->onDelete('cascade');
            $table->foreign('chambre_id')->references('id')->on('chambres')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitalization_room_stays');
    }
};


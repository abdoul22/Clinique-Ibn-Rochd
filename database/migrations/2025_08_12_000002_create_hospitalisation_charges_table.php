<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospitalisation_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospitalisation_id');
            $table->unsignedBigInteger('room_stay_id')->nullable();
            $table->enum('type', ['room_day', 'examen', 'service', 'pharmacy']);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('description_snapshot');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('total_price', 10, 2);
            $table->decimal('part_medecin', 10, 2)->default(0);
            $table->decimal('part_cabinet', 10, 2)->default(0);
            $table->boolean('is_pharmacy')->default(false);
            $table->boolean('is_billed')->default(false);
            $table->dateTime('billed_at')->nullable();
            $table->unsignedBigInteger('caisse_id')->nullable();
            $table->timestamps();

            $table->foreign('hospitalisation_id')->references('id')->on('hospitalisations')->onDelete('cascade');
            $table->foreign('room_stay_id')->references('id')->on('hospitalization_room_stays')->onDelete('set null');
            $table->foreign('caisse_id')->references('id')->on('caisses')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitalisation_charges');
    }
};

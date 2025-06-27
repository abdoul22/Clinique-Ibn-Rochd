<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->integer('montant')->nullable();
            $table->string('source')->default('manuelle'); // ou null si tu préfères
            $table->foreignId('mode_paiement_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};

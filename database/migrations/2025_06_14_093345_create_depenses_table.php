<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->integer('montant')->nullable();
            $table->string('source')->default('manuelle');
            $table->foreignId('mode_paiement_id')->nullable()->constrained('mode_paiements')->nullOnDelete();
            $table->foreignId('etat_caisse_id')->nullable()->constrained('etat_caisses')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};

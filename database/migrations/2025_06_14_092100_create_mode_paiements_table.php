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
        Schema::create('mode_paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caisse_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['espèces', 'bankily', 'masrivi','sedad']);
            $table->decimal('montant', 10, 2);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mode_paiements');
    }
};



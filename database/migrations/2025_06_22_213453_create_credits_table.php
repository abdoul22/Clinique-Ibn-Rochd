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
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            // Morphs : crée automatiquement source_type et source_id
            $table->morphs('source');
            $table->decimal('montant', 10, 2);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->string('status')->default('non payé'); 
            $table->foreignId('caisse_id')->nullable()->constrained()->nullOnDelete();
            $table->string('statut')->default('non payé');
            $table->foreignId('mode_paiement_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }




    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};

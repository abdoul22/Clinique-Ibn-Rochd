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
            $table->string('type'); // 'personnel' ou 'assurance'
            $table->unsignedBigInteger('source_id'); // ID du personnel ou de l'assurance
            $table->decimal('montant', 10, 2);
            $table->enum('statut', ['non payé', 'partiellement payé', 'payé'])->default('non payé');
            $table->integer('montant_paye')->default(0);
            $table->string('status')->default('non payé');
            $table->morphs('source');
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

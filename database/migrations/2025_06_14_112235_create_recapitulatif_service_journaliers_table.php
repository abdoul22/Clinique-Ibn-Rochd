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
        Schema::create('recapitulatif_service_journaliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idsvc')->constrained('services')->onDelete('cascade');
            $table->decimal('total', 10, 2);
            $table->date('date');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recapitulatif_service_journaliers');
    }
};

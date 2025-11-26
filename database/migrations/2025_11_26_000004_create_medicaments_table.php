<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicaments', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('forme')->nullable(); // sirop, comprimé, gélule, etc.
            $table->string('dosage')->nullable(); // 500mg, 5ml, etc.
            $table->string('fabricant')->nullable();
            $table->text('description')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            
            $table->index('nom');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicaments');
    }
};


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
        Schema::table('services', function (Blueprint $table) {
            $table->enum('type_service', [
                'consultations',
                'examens',
                'pharmacie',
                'infirmerie',
                'bloc',
                'laboratoire',
                'hospitalisation',
                'dentaire'
            ])->default('consultations')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->enum('type_service', [
                'examen',
                'medicament',
                'consultation',
                'pharmacie',
                'medecins'
            ])->default('examen')->change();
        });
    }
};

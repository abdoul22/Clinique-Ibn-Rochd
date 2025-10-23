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
        Schema::table('hospitalisations', function (Blueprint $table) {
            $table->foreignId('pharmacien_id')->nullable()->constrained('medecins')->onDelete('set null')->after('medecin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitalisations', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Medecin::class, 'pharmacien_id');
        });
    }
};

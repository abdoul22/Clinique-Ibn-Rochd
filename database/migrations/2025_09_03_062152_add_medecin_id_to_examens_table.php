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
        Schema::table('examens', function (Blueprint $table) {
            $table->unsignedBigInteger('medecin_id')->nullable()->after('idsvc');
            $table->foreign('medecin_id')->references('id')->on('medecins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('examens', function (Blueprint $table) {
            $table->dropForeign(['medecin_id']);
            $table->dropColumn('medecin_id');
        });
    }
};

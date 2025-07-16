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
            // Supprimer les colonnes obsolètes qui interfèrent avec les relations
            $table->dropColumn(['chambre', 'lit']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitalisations', function (Blueprint $table) {
            // Recréer les colonnes en cas de rollback
            $table->string('chambre')->nullable();
            $table->string('lit')->nullable();
        });
    }
};

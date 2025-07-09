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
            $table->foreignId('pharmacie_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type_service', ['examen', 'medicament', 'consultation'])->default('examen');
            $table->decimal('prix', 10, 2)->nullable();
            $table->integer('quantite_defaut')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['pharmacie_id']);
            $table->dropColumn(['pharmacie_id', 'type_service', 'prix', 'quantite_defaut']);
        });
    }
};

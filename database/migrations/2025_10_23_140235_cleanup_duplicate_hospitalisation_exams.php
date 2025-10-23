<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete all "Hospitalisation" exams with 0 tariff that are associated with HOSPITALISATION service
        // These are generated dynamically when billing hospitalizations
        DB::statement('
            DELETE FROM examens
            WHERE nom = "Hospitalisation"
            AND tarif = 0
            AND idsvc IN (
                SELECT id FROM services
                WHERE type_service = "HOSPITALISATION"
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed - these are auto-generated exams that shouldn't exist anyway
    }
};

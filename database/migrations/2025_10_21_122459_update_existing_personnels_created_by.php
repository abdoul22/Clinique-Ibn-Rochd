<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mettre à jour les personnels existants qui n'ont pas de created_by
        // Les personnels avec user_id sont considérés comme créés par le système
        // Les autres sont considérés comme créés par superadmin
        DB::table('personnels')
            ->whereNull('created_by')
            ->whereNotNull('user_id')
            ->update(['created_by' => 'system']);

        DB::table('personnels')
            ->whereNull('created_by')
            ->whereNull('user_id')
            ->update(['created_by' => 'superadmin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remettre created_by à null pour les personnels mis à jour
        DB::table('personnels')
            ->whereIn('created_by', ['system', 'superadmin'])
            ->update(['created_by' => null]);
    }
};

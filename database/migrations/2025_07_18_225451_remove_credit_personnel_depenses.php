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
        // Supprimer les dépenses liées aux crédits personnel
        // Ces dépenses ne doivent pas exister car les crédits personnel sont payés par déduction salaire
        DB::table('depenses')
            ->where('mode_paiement_id', 'salaire')
            ->orWhere('nom', 'like', '%Déduction salaire%')
            ->orWhere('nom', 'like', '%Crédit personnel%')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette migration ne peut pas être annulée car elle supprime des données
        // En cas de besoin, utilisez la commande artisan clean:credit-depenses --backup
        // qui crée une sauvegarde avant suppression
    }
};

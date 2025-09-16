<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vérifier si on est en environnement de test (SQLite)
        if (config('database.default') === 'sqlite') {
            // Pour SQLite, on ne peut pas modifier les colonnes ENUM, on skip cette migration
            return;
        }

        // 1) Étape de transition: passer provisoirement la colonne en VARCHAR pour éviter l'erreur de doublon ENUM
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE services MODIFY type_service VARCHAR(100) NULL");

        // 2) Harmoniser les anciennes valeurs vers les nouvelles (MEDICAMENT => PHARMACIE)
        \Illuminate\Support\Facades\DB::table('services')->whereIn('type_service', ['laboratoire', 'Laboratoire'])->update(['type_service' => 'LABORATOIRE']);
        \Illuminate\Support\Facades\DB::table('services')->whereIn('type_service', ['pharmacie', 'Pharmacie', 'medicament', 'Medicament'])->update(['type_service' => 'PHARMACIE']);
        \Illuminate\Support\Facades\DB::table('services')->whereIn('type_service', ['dentaire', 'Dentaire', 'MÉDECINE DENTAIRE'])->update(['type_service' => 'MEDECINE DENTAIRE']);
        \Illuminate\Support\Facades\DB::table('services')->whereIn('type_service', ['imagerie', 'Imagerie', 'IMAGERIE MÉDICALE'])->update(['type_service' => 'IMAGERIE MEDICALE']);
        \Illuminate\Support\Facades\DB::table('services')->whereIn('type_service', ['consultations', 'consultation', 'Consultations', 'Consultation', 'medecins', 'Médecins'])->update(['type_service' => 'CONSULTATIONS EXTERNES']);
        \Illuminate\Support\Facades\DB::table('services')->whereIn('type_service', ['hospitalisation', 'Hospitalisation'])->update(['type_service' => 'HOSPITALISATION']);
        \Illuminate\Support\Facades\DB::table('services')->whereIn('type_service', ['bloc', 'Bloc', '     '])->update(['type_service' => 'BLOC OPERATOIRE']);
        \Illuminate\Support\Facades\DB::table('services')->whereIn('type_service', ['infirmerie', 'Infirmerie', 'INFIRMIER', 'INFIRMIERIE'])->update(['type_service' => 'INFIRMERIE']);
        \Illuminate\Support\Facades\DB::table('services')->whereIn('type_service', ['examens', 'examen', 'Examens', 'Examen'])->update(['type_service' => 'EXPLORATIONS FONCTIONNELLES']);

        // 3) Enum final: uniquement nouvelles valeurs (via SQL brut pour MySQL)
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE services MODIFY type_service ENUM('LABORATOIRE','PHARMACIE','MEDECINE DENTAIRE','IMAGERIE MEDICALE','CONSULTATIONS EXTERNES','HOSPITALISATION','BLOC OPERATOIRE','INFIRMERIE','EXPLORATIONS FONCTIONNELLES') NOT NULL DEFAULT 'LABORATOIRE'"
        );
    }

    public function down(): void
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
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospitalisations', function (Blueprint $table) {
            if (!Schema::hasColumn('hospitalisations', 'assurance_id')) {
                $table->unsignedBigInteger('assurance_id')->nullable()->after('service_id');
                $table->foreign('assurance_id')->references('id')->on('assurances')->onDelete('set null');
            }
            if (!Schema::hasColumn('hospitalisations', 'couverture')) {
                $table->decimal('couverture', 5, 2)->default(0)->after('assurance_id');
            }
            if (!Schema::hasColumn('hospitalisations', 'admission_at')) {
                $table->dateTime('admission_at')->nullable()->after('date_entree');
            }
            if (!Schema::hasColumn('hospitalisations', 'discharge_at')) {
                $table->dateTime('discharge_at')->nullable()->after('date_sortie');
            }
            if (!Schema::hasColumn('hospitalisations', 'next_charge_due_at')) {
                $table->dateTime('next_charge_due_at')->nullable()->after('discharge_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hospitalisations', function (Blueprint $table) {
            if (Schema::hasColumn('hospitalisations', 'next_charge_due_at')) {
                $table->dropColumn('next_charge_due_at');
            }
            if (Schema::hasColumn('hospitalisations', 'discharge_at')) {
                $table->dropColumn('discharge_at');
            }
            if (Schema::hasColumn('hospitalisations', 'admission_at')) {
                $table->dropColumn('admission_at');
            }
            if (Schema::hasColumn('hospitalisations', 'couverture')) {
                $table->dropColumn('couverture');
            }
            if (Schema::hasColumn('hospitalisations', 'assurance_id')) {
                $table->dropForeign(['assurance_id']);
                $table->dropColumn('assurance_id');
            }
        });
    }
};

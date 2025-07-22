<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('mode_paiements', function (Blueprint $table) {
            $table->string('source')->nullable()->after('montant');
        });
    }

    public function down()
    {
        Schema::table('mode_paiements', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};

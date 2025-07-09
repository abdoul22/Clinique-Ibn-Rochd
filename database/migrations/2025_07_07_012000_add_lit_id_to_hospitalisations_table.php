<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('hospitalisations', function (Blueprint $table) {
            $table->unsignedBigInteger('lit_id')->nullable()->after('service_id');
            $table->foreign('lit_id')->references('id')->on('lits')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('hospitalisations', function (Blueprint $table) {
            $table->dropForeign(['lit_id']);
            $table->dropColumn('lit_id');
        });
    }
};

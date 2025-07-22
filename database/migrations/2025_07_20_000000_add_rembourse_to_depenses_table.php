<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('depenses', function (Blueprint $table) {
            $table->boolean('rembourse')->default(false)->after('credit_id');
        });
    }

    public function down()
    {
        Schema::table('depenses', function (Blueprint $table) {
            $table->dropColumn('rembourse');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personnel_id')->constrained('personnels')->onDelete('cascade');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('montant_net', 12, 2)->default(0);
            $table->string('mode', 50)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['personnel_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};

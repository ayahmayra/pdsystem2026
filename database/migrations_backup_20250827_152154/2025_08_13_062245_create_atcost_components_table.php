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
        Schema::create('atcost_components', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // RORO, PARKIR_INAP, TOLL, RAPID_TEST, TAXI
            $table->string('name'); // Nama komponen
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atcost_components');
    }
};

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
        Schema::create('sppd_divisum_signoffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppd_id')->constrained('sppd')->onDelete('cascade');
            $table->string('signed_place');
            $table->date('signed_date');
            $table->string('signed_by_name');
            $table->string('signed_by_position');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sppd_divisum_signoffs');
    }
};

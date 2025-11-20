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
        Schema::create('sppd_transport_modes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppd_id')->constrained('sppd')->onDelete('cascade');
            $table->foreignId('transport_mode_id')->constrained('transport_modes')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['sppd_id', 'transport_mode_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sppd_transport_modes');
    }
};

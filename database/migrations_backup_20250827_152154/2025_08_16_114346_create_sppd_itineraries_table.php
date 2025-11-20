<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sppd_itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppd_id')->constrained('sppd')->onDelete('cascade');
            $table->integer('leg_no');
            $table->date('date')->nullable();
            $table->string('from_place');
            $table->string('to_place');
            $table->string('mode_detail');
            $table->string('ticket_no')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sppd_itineraries');
    }
};

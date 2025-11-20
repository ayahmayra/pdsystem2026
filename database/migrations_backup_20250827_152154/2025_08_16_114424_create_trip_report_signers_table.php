<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_report_signers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_report_id')->constrained('trip_reports')->onDelete('cascade');
            $table->string('name');
            $table->string('nip')->nullable();
            $table->string('position')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_report_signers');
    }
};

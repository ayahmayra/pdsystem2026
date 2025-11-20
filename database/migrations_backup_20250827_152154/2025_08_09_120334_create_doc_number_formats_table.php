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
        Schema::create('doc_number_formats', function (Blueprint $table) {
            $table->id();
            $table->string('doc_type')->unique(); // nodinis|spt|sppd
            $table->string('format');             // SPPD/{y}/{m}/{seq:4}
            $table->string('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_number_formats');
    }
};

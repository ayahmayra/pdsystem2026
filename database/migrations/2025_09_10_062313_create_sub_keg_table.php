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
        Schema::create('sub_keg', function (Blueprint $table) {
            $table->id();
            $table->string('kode_subkeg')->unique();
            $table->string('nama_subkeg');
            $table->unsignedBigInteger('id_unit');
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('id_unit')->references('id')->on('units')->onDelete('cascade');
            
            // Index for better performance
            $table->index('id_unit');
            $table->index('kode_subkeg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_keg');
    }
};

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
        Schema::table('rekening_belanja', function (Blueprint $table) {
            // Hapus unique constraint dari kode_rekening
            $table->dropUnique(['kode_rekening']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekening_belanja', function (Blueprint $table) {
            // Kembalikan unique constraint ke kode_rekening
            $table->unique('kode_rekening');
        });
    }
};

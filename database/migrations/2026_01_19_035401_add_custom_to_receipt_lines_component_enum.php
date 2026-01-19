<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Untuk MySQL, kita perlu menggunakan raw SQL untuk mengubah ENUM
        // karena Laravel Schema builder tidak support modify ENUM dengan baik
        DB::statement("ALTER TABLE receipt_lines MODIFY COLUMN component ENUM(
            'PERDIEM',
            'REPRESENTASI',
            'LODGING',
            'AIRFARE',
            'INTRA_PROV',
            'INTRA_DISTRICT',
            'OFFICIAL_VEHICLE',
            'TAXI',
            'RORO',
            'TOLL',
            'PARKIR_INAP',
            'RAPID_TEST',
            'LAINNYA',
            'CUSTOM'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: hapus 'CUSTOM' dari ENUM
        // WARNING: Ini akan gagal jika ada data dengan component='CUSTOM'
        DB::statement("ALTER TABLE receipt_lines MODIFY COLUMN component ENUM(
            'PERDIEM',
            'REPRESENTASI',
            'LODGING',
            'AIRFARE',
            'INTRA_PROV',
            'INTRA_DISTRICT',
            'OFFICIAL_VEHICLE',
            'TAXI',
            'RORO',
            'TOLL',
            'PARKIR_INAP',
            'RAPID_TEST',
            'LAINNYA'
        ) NOT NULL");
    }
};

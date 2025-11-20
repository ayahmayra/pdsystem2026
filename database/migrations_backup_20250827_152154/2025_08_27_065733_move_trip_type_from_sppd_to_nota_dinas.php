<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added missing import for DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add trip_type to nota_dinas table
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->enum('trip_type', ['LUAR_DAERAH','DALAM_DAERAH_GT8H','DALAM_DAERAH_LE8H','DIKLAT'])->after('end_date');
        });

        // Copy existing trip_type data from sppd to nota_dinas
        // We'll do this in a separate step to ensure data integrity
        DB::statement("
            UPDATE nota_dinas 
            SET trip_type = (
                SELECT sppd.trip_type 
                FROM sppd 
                JOIN spt ON sppd.spt_id = spt.id 
                WHERE spt.nota_dinas_id = nota_dinas.id 
                LIMIT 1
            )
            WHERE EXISTS (
                SELECT 1 
                FROM sppd 
                JOIN spt ON sppd.spt_id = spt.id 
                WHERE spt.nota_dinas_id = nota_dinas.id
            )
        ");

        // Set default value for nota_dinas that don't have sppd yet
        DB::statement("
            UPDATE nota_dinas 
            SET trip_type = 'LUAR_DAERAH' 
            WHERE trip_type IS NULL
        ");

        // Remove trip_type from sppd table
        Schema::table('sppd', function (Blueprint $table) {
            $table->dropColumn('trip_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add trip_type back to sppd table
        Schema::table('sppd', function (Blueprint $table) {
            $table->enum('trip_type', ['LUAR_DAERAH','DALAM_DAERAH_GT8H','DALAM_DAERAH_LE8H','DIKLAT'])->after('transport_mode_id');
        });

        // Copy trip_type data back from nota_dinas to sppd
        DB::statement("
            UPDATE sppd 
            SET trip_type = (
                SELECT nota_dinas.trip_type 
                FROM nota_dinas 
                JOIN spt ON nota_dinas.id = spt.nota_dinas_id 
                WHERE spt.id = sppd.spt_id
            )
        ");

        // Remove trip_type from nota_dinas table
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->dropColumn('trip_type');
        });
    }
};

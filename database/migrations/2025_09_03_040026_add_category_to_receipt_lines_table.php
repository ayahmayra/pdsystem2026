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
        Schema::table('receipt_lines', function (Blueprint $table) {
            $table->enum('category', [
                'transport', 'lodging', 'per_diem', 'representation', 'other'
            ])->after('component')->nullable();
        });

        // Populate category based on existing component values
        DB::statement("
            UPDATE receipt_lines SET category = CASE 
                WHEN component IN ('AIRFARE', 'INTRA_PROV', 'INTRA_DISTRICT', 'OFFICIAL_VEHICLE', 'TAXI', 'RORO', 'TOLL') THEN 'transport'
                WHEN component = 'LODGING' THEN 'lodging'
                WHEN component = 'PERDIEM' THEN 'per_diem'
                WHEN component = 'REPRESENTASI' THEN 'representation'
                ELSE 'other'
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_lines', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};

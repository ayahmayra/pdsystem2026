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
        Schema::table('sppd', function (Blueprint $table) {
            $table->string('travel_grade_code', 10)->nullable()->after('signed_by_user_position_echelon_id_snapshot')->comment('Snapshot kode tingkat biaya perjalanan dinas dari participant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            $table->dropColumn('travel_grade_code');
        });
    }
};

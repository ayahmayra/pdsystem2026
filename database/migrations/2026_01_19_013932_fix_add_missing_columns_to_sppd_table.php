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
            // Check and add travel_grade_code if not exists
            if (!Schema::hasColumn('sppd', 'travel_grade_code')) {
                $table->string('travel_grade_code', 10)
                      ->nullable()
                      ->after('signed_by_user_position_echelon_id_snapshot')
                      ->comment('Snapshot kode tingkat biaya perjalanan dinas dari participant');
            }
            
            // Check and add signed_by_user_budget_role_snapshot if not exists
            if (!Schema::hasColumn('sppd', 'signed_by_user_budget_role_snapshot')) {
                $table->enum('signed_by_user_budget_role_snapshot', ['pengguna_anggaran', 'kuasa_pengguna_anggaran'])
                      ->nullable()
                      ->after('travel_grade_code')
                      ->comment('Snapshot role pengelolaan anggaran penandatangan (Pengguna Anggaran atau Kuasa Pengguna Anggaran)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            if (Schema::hasColumn('sppd', 'travel_grade_code')) {
                $table->dropColumn('travel_grade_code');
            }
            
            if (Schema::hasColumn('sppd', 'signed_by_user_budget_role_snapshot')) {
                $table->dropColumn('signed_by_user_budget_role_snapshot');
            }
        });
    }
};

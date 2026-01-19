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
            $table->enum('signed_by_user_budget_role_snapshot', ['pengguna_anggaran', 'kuasa_pengguna_anggaran'])
                  ->nullable()
                  ->after('travel_grade_code')
                  ->comment('Snapshot role pengelolaan anggaran penandatangan (Pengguna Anggaran atau Kuasa Pengguna Anggaran)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            $table->dropColumn('signed_by_user_budget_role_snapshot');
        });
    }
};

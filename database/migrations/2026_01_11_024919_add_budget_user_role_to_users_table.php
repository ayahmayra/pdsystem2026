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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('budget_user_role', ['pengguna_anggaran', 'kuasa_pengguna_anggaran'])
                  ->nullable()
                  ->after('is_non_staff')
                  ->comment('Role user dalam pengelolaan anggaran: Pengguna Anggaran atau Kuasa Pengguna Anggaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('budget_user_role');
        });
    }
};

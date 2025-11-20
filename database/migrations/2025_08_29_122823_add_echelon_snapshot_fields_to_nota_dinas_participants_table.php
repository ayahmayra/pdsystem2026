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
        Schema::table('nota_dinas_participants', function (Blueprint $table) {
            // Add echelon snapshot field
            $table->unsignedBigInteger('user_position_echelon_id_snapshot')->nullable()->after('user_rank_code_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_dinas_participants', function (Blueprint $table) {
            $table->dropColumn('user_position_echelon_id_snapshot');
        });
    }
};

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
            // Snapshot fields for participant user data
            $table->string('user_name_snapshot')->nullable()->after('user_id');
            $table->string('user_gelar_depan_snapshot')->nullable()->after('user_name_snapshot');
            $table->string('user_gelar_belakang_snapshot')->nullable()->after('user_gelar_depan_snapshot');
            $table->string('user_nip_snapshot')->nullable()->after('user_gelar_belakang_snapshot');
            $table->unsignedBigInteger('user_unit_id_snapshot')->nullable()->after('user_nip_snapshot');
            $table->string('user_unit_name_snapshot')->nullable()->after('user_unit_id_snapshot');
            $table->unsignedBigInteger('user_position_id_snapshot')->nullable()->after('user_unit_name_snapshot');
            $table->string('user_position_name_snapshot')->nullable()->after('user_position_id_snapshot');
            $table->string('user_position_desc_snapshot')->nullable()->after('user_position_name_snapshot');
            $table->unsignedBigInteger('user_rank_id_snapshot')->nullable()->after('user_position_desc_snapshot');
            $table->string('user_rank_name_snapshot')->nullable()->after('user_rank_id_snapshot');
            $table->string('user_rank_code_snapshot')->nullable()->after('user_rank_name_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_dinas_participants', function (Blueprint $table) {
            $table->dropColumn([
                'user_name_snapshot',
                'user_gelar_depan_snapshot',
                'user_gelar_belakang_snapshot',
                'user_nip_snapshot',
                'user_unit_id_snapshot',
                'user_unit_name_snapshot',
                'user_position_id_snapshot',
                'user_position_name_snapshot',
                'user_position_desc_snapshot',
                'user_rank_id_snapshot',
                'user_rank_name_snapshot',
                'user_rank_code_snapshot',
            ]);
        });
    }
};

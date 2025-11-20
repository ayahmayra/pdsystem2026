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
        Schema::table('nota_dinas', function (Blueprint $table) {
            // Snapshot fields for from_user (penandatangan)
            $table->string('from_user_name_snapshot')->nullable()->after('from_user_id');
            $table->string('from_user_gelar_depan_snapshot')->nullable()->after('from_user_name_snapshot');
            $table->string('from_user_gelar_belakang_snapshot')->nullable()->after('from_user_gelar_depan_snapshot');
            $table->string('from_user_nip_snapshot')->nullable()->after('from_user_gelar_belakang_snapshot');
            $table->unsignedBigInteger('from_user_unit_id_snapshot')->nullable()->after('from_user_nip_snapshot');
            $table->string('from_user_unit_name_snapshot')->nullable()->after('from_user_unit_id_snapshot');
            $table->unsignedBigInteger('from_user_position_id_snapshot')->nullable()->after('from_user_unit_name_snapshot');
            $table->string('from_user_position_name_snapshot')->nullable()->after('from_user_position_id_snapshot');
            $table->string('from_user_position_desc_snapshot')->nullable()->after('from_user_position_name_snapshot');
            $table->unsignedBigInteger('from_user_rank_id_snapshot')->nullable()->after('from_user_position_desc_snapshot');
            $table->string('from_user_rank_name_snapshot')->nullable()->after('from_user_rank_id_snapshot');
            $table->string('from_user_rank_code_snapshot')->nullable()->after('from_user_rank_name_snapshot');
            
            // Snapshot fields for to_user (tujuan)
            $table->string('to_user_name_snapshot')->nullable()->after('from_user_rank_code_snapshot');
            $table->string('to_user_gelar_depan_snapshot')->nullable()->after('to_user_name_snapshot');
            $table->string('to_user_gelar_belakang_snapshot')->nullable()->after('to_user_gelar_depan_snapshot');
            $table->string('to_user_nip_snapshot')->nullable()->after('to_user_gelar_belakang_snapshot');
            $table->unsignedBigInteger('to_user_unit_id_snapshot')->nullable()->after('to_user_nip_snapshot');
            $table->string('to_user_unit_name_snapshot')->nullable()->after('to_user_unit_id_snapshot');
            $table->unsignedBigInteger('to_user_position_id_snapshot')->nullable()->after('to_user_unit_name_snapshot');
            $table->string('to_user_position_name_snapshot')->nullable()->after('to_user_position_id_snapshot');
            $table->string('to_user_position_desc_snapshot')->nullable()->after('to_user_position_name_snapshot');
            $table->unsignedBigInteger('to_user_rank_id_snapshot')->nullable()->after('to_user_position_desc_snapshot');
            $table->string('to_user_rank_name_snapshot')->nullable()->after('to_user_rank_id_snapshot');
            $table->string('to_user_rank_code_snapshot')->nullable()->after('to_user_rank_name_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            // Drop from_user snapshot fields
            $table->dropColumn([
                'from_user_name_snapshot',
                'from_user_gelar_depan_snapshot',
                'from_user_gelar_belakang_snapshot',
                'from_user_nip_snapshot',
                'from_user_unit_id_snapshot',
                'from_user_unit_name_snapshot',
                'from_user_position_id_snapshot',
                'from_user_position_name_snapshot',
                'from_user_position_desc_snapshot',
                'from_user_rank_id_snapshot',
                'from_user_rank_name_snapshot',
                'from_user_rank_code_snapshot',
            ]);
            
            // Drop to_user snapshot fields
            $table->dropColumn([
                'to_user_name_snapshot',
                'to_user_gelar_depan_snapshot',
                'to_user_gelar_belakang_snapshot',
                'to_user_nip_snapshot',
                'to_user_unit_id_snapshot',
                'to_user_unit_name_snapshot',
                'to_user_position_id_snapshot',
                'to_user_position_name_snapshot',
                'to_user_position_desc_snapshot',
                'to_user_rank_id_snapshot',
                'to_user_rank_name_snapshot',
                'to_user_rank_code_snapshot',
            ]);
        });
    }
};

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
        Schema::table('spt', function (Blueprint $table) {
            // Snapshot fields for signed_by_user data
            $table->string('signed_by_user_name_snapshot')->nullable()->after('signed_by_user_id');
            $table->string('signed_by_user_gelar_depan_snapshot')->nullable()->after('signed_by_user_name_snapshot');
            $table->string('signed_by_user_gelar_belakang_snapshot')->nullable()->after('signed_by_user_gelar_depan_snapshot');
            $table->string('signed_by_user_nip_snapshot')->nullable()->after('signed_by_user_gelar_belakang_snapshot');
            $table->unsignedBigInteger('signed_by_user_unit_id_snapshot')->nullable()->after('signed_by_user_nip_snapshot');
            $table->string('signed_by_user_unit_name_snapshot')->nullable()->after('signed_by_user_unit_id_snapshot');
            $table->unsignedBigInteger('signed_by_user_position_id_snapshot')->nullable()->after('signed_by_user_unit_name_snapshot');
            $table->string('signed_by_user_position_name_snapshot')->nullable()->after('signed_by_user_position_id_snapshot');
            $table->string('signed_by_user_position_desc_snapshot')->nullable()->after('signed_by_user_position_name_snapshot');
            $table->unsignedBigInteger('signed_by_user_rank_id_snapshot')->nullable()->after('signed_by_user_position_desc_snapshot');
            $table->string('signed_by_user_rank_name_snapshot')->nullable()->after('signed_by_user_rank_id_snapshot');
            $table->string('signed_by_user_rank_code_snapshot')->nullable()->after('signed_by_user_rank_name_snapshot');
            $table->unsignedBigInteger('signed_by_user_position_echelon_id_snapshot')->nullable()->after('signed_by_user_rank_code_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spt', function (Blueprint $table) {
            $table->dropColumn([
                'signed_by_user_name_snapshot',
                'signed_by_user_gelar_depan_snapshot',
                'signed_by_user_gelar_belakang_snapshot',
                'signed_by_user_nip_snapshot',
                'signed_by_user_unit_id_snapshot',
                'signed_by_user_unit_name_snapshot',
                'signed_by_user_position_id_snapshot',
                'signed_by_user_position_name_snapshot',
                'signed_by_user_position_desc_snapshot',
                'signed_by_user_rank_id_snapshot',
                'signed_by_user_rank_name_snapshot',
                'signed_by_user_rank_code_snapshot',
                'signed_by_user_position_echelon_id_snapshot',
            ]);
        });
    }
};

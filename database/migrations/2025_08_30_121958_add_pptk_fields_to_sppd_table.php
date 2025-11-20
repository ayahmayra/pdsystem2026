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
            // PPTK fields
            $table->unsignedBigInteger('pptk_user_id')->nullable()->after('signed_by_user_id');
            $table->foreign('pptk_user_id')->references('id')->on('users')->onDelete('set null');
            
            // PPTK snapshot fields
            $table->string('pptk_user_name_snapshot')->nullable()->after('pptk_user_id');
            $table->string('pptk_user_gelar_depan_snapshot')->nullable()->after('pptk_user_name_snapshot');
            $table->string('pptk_user_gelar_belakang_snapshot')->nullable()->after('pptk_user_gelar_depan_snapshot');
            $table->string('pptk_user_nip_snapshot')->nullable()->after('pptk_user_gelar_belakang_snapshot');
            $table->unsignedBigInteger('pptk_user_unit_id_snapshot')->nullable()->after('pptk_user_nip_snapshot');
            $table->string('pptk_user_unit_name_snapshot')->nullable()->after('pptk_user_unit_id_snapshot');
            $table->unsignedBigInteger('pptk_user_position_id_snapshot')->nullable()->after('pptk_user_unit_name_snapshot');
            $table->string('pptk_user_position_name_snapshot')->nullable()->after('pptk_user_position_id_snapshot');
            $table->text('pptk_user_position_desc_snapshot')->nullable()->after('pptk_user_position_name_snapshot');
            $table->unsignedBigInteger('pptk_user_rank_id_snapshot')->nullable()->after('pptk_user_position_desc_snapshot');
            $table->string('pptk_user_rank_name_snapshot')->nullable()->after('pptk_user_rank_id_snapshot');
            $table->string('pptk_user_rank_code_snapshot')->nullable()->after('pptk_user_rank_name_snapshot');
            $table->unsignedBigInteger('pptk_user_position_echelon_id_snapshot')->nullable()->after('pptk_user_rank_code_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            $table->dropForeign(['pptk_user_id']);
            $table->dropColumn([
                'pptk_user_id',
                'pptk_user_name_snapshot',
                'pptk_user_gelar_depan_snapshot',
                'pptk_user_gelar_belakang_snapshot',
                'pptk_user_nip_snapshot',
                'pptk_user_unit_id_snapshot',
                'pptk_user_unit_name_snapshot',
                'pptk_user_position_id_snapshot',
                'pptk_user_position_name_snapshot',
                'pptk_user_position_desc_snapshot',
                'pptk_user_rank_id_snapshot',
                'pptk_user_rank_name_snapshot',
                'pptk_user_rank_code_snapshot',
                'pptk_user_position_echelon_id_snapshot',
            ]);
        });
    }
};

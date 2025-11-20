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
        Schema::table('receipts', function (Blueprint $table) {
            // Field untuk "Setuju dibayar" - merujuk ke penandatangan Nota Dinas
            $table->unsignedBigInteger('approved_by_user_id')->nullable()->after('payee_user_id');
            $table->foreign('approved_by_user_id')->references('id')->on('users')->onDelete('set null');
            
            // Snapshot fields untuk approved_by_user
            $table->string('approved_by_user_name_snapshot')->nullable()->after('approved_by_user_id');
            $table->string('approved_by_user_gelar_depan_snapshot')->nullable()->after('approved_by_user_name_snapshot');
            $table->string('approved_by_user_gelar_belakang_snapshot')->nullable()->after('approved_by_user_gelar_depan_snapshot');
            $table->string('approved_by_user_nip_snapshot')->nullable()->after('approved_by_user_gelar_belakang_snapshot');
            $table->unsignedBigInteger('approved_by_user_unit_id_snapshot')->nullable()->after('approved_by_user_nip_snapshot');
            $table->string('approved_by_user_unit_name_snapshot')->nullable()->after('approved_by_user_unit_id_snapshot');
            $table->unsignedBigInteger('approved_by_user_position_id_snapshot')->nullable()->after('approved_by_user_unit_name_snapshot');
            $table->string('approved_by_user_position_name_snapshot')->nullable()->after('approved_by_user_position_id_snapshot');
            $table->text('approved_by_user_position_desc_snapshot')->nullable()->after('approved_by_user_position_name_snapshot');
            $table->unsignedBigInteger('approved_by_user_rank_id_snapshot')->nullable()->after('approved_by_user_position_desc_snapshot');
            $table->string('approved_by_user_rank_name_snapshot')->nullable()->after('approved_by_user_rank_id_snapshot');
            $table->string('approved_by_user_rank_code_snapshot')->nullable()->after('approved_by_user_rank_name_snapshot');
            
            // Kode Rekening Kegiatan
            $table->string('account_code')->nullable()->after('approved_by_user_rank_code_snapshot');
            
            // Field untuk Bendahara
            $table->unsignedBigInteger('treasurer_user_id')->nullable()->after('account_code');
            $table->foreign('treasurer_user_id')->references('id')->on('users')->onDelete('set null');
            
            // Snapshot fields untuk bendahara
            $table->string('treasurer_user_name_snapshot')->nullable()->after('treasurer_user_id');
            $table->string('treasurer_user_gelar_depan_snapshot')->nullable()->after('treasurer_user_name_snapshot');
            $table->string('treasurer_user_gelar_belakang_snapshot')->nullable()->after('treasurer_user_gelar_depan_snapshot');
            $table->string('treasurer_user_nip_snapshot')->nullable()->after('treasurer_user_gelar_belakang_snapshot');
            $table->unsignedBigInteger('treasurer_user_unit_id_snapshot')->nullable()->after('treasurer_user_nip_snapshot');
            $table->string('treasurer_user_unit_name_snapshot')->nullable()->after('treasurer_user_unit_id_snapshot');
            $table->unsignedBigInteger('treasurer_user_position_id_snapshot')->nullable()->after('treasurer_user_unit_name_snapshot');
            $table->string('treasurer_user_position_name_snapshot')->nullable()->after('treasurer_user_position_id_snapshot');
            $table->text('treasurer_user_position_desc_snapshot')->nullable()->after('treasurer_user_position_name_snapshot');
            $table->unsignedBigInteger('treasurer_user_rank_id_snapshot')->nullable()->after('treasurer_user_position_desc_snapshot');
            $table->string('treasurer_user_rank_name_snapshot')->nullable()->after('treasurer_user_rank_id_snapshot');
            $table->string('treasurer_user_rank_code_snapshot')->nullable()->after('treasurer_user_rank_name_snapshot');
            
            // Titel Bendahara
            $table->enum('treasurer_title', ['Bendahara Pengeluaran', 'Bendahara Pengeluaran Pembantu'])->nullable()->after('treasurer_user_rank_code_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['approved_by_user_id']);
            $table->dropForeign(['treasurer_user_id']);
            
            // Drop columns
            $table->dropColumn([
                'approved_by_user_id',
                'approved_by_user_name_snapshot',
                'approved_by_user_gelar_depan_snapshot',
                'approved_by_user_gelar_belakang_snapshot',
                'approved_by_user_nip_snapshot',
                'approved_by_user_unit_id_snapshot',
                'approved_by_user_unit_name_snapshot',
                'approved_by_user_position_id_snapshot',
                'approved_by_user_position_name_snapshot',
                'approved_by_user_position_desc_snapshot',
                'approved_by_user_rank_id_snapshot',
                'approved_by_user_rank_name_snapshot',
                'approved_by_user_rank_code_snapshot',
                'account_code',
                'treasurer_user_id',
                'treasurer_user_name_snapshot',
                'treasurer_user_gelar_depan_snapshot',
                'treasurer_user_gelar_belakang_snapshot',
                'treasurer_user_nip_snapshot',
                'treasurer_user_unit_id_snapshot',
                'treasurer_user_unit_name_snapshot',
                'treasurer_user_position_id_snapshot',
                'treasurer_user_position_name_snapshot',
                'treasurer_user_position_desc_snapshot',
                'treasurer_user_rank_id_snapshot',
                'treasurer_user_rank_name_snapshot',
                'treasurer_user_rank_code_snapshot',
                'treasurer_title'
            ]);
        });
    }
};

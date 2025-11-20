<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column exists before attempting to modify table
        if (Schema::hasColumn('receipts', 'approved_by_user_id')) {
            // Get the actual foreign key name from database
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'receipts' 
                AND COLUMN_NAME = 'approved_by_user_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            Schema::table('receipts', function (Blueprint $table) use ($foreignKeys) {
                // Drop foreign key constraint if it exists
                if (!empty($foreignKeys)) {
                    foreach ($foreignKeys as $fk) {
                        try {
                            $table->dropForeign($fk->CONSTRAINT_NAME);
                        } catch (\Exception $e) {
                            // Foreign key might not exist, continue
                        }
                    }
                }
                
                // Remove approved_by_user_id and all its snapshot fields
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
                ]);
            });
        }
        
        // Make account_code nullable if column exists
        if (Schema::hasColumn('receipts', 'account_code')) {
            Schema::table('receipts', function (Blueprint $table) {
                $table->string('account_code')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // Add back approved_by_user_id and its snapshot fields
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
            
            // Make account_code required again
            $table->string('account_code')->nullable(false)->change();
        });
    }
};

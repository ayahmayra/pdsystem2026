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
        if (Schema::hasColumn('sppd', 'user_id')) {
            // Get the actual foreign key name from database
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'sppd' 
                AND COLUMN_NAME = 'user_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            Schema::table('sppd', function (Blueprint $table) use ($foreignKeys) {
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
                
                // Remove user_id field since 1 SPPD now represents all participants
                $table->dropColumn('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            // Add back user_id field if needed to rollback
            $table->unsignedBigInteger('user_id')->nullable()->after('spt_id');
            // Add back foreign key constraint
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};

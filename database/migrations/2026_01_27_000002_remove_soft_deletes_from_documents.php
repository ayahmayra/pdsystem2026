<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Remove soft deletes from main documents to avoid numbering conflicts.
     * Documents will now be hard deleted to prevent duplicate document numbers.
     */
    public function up(): void
    {
        // Remove deleted_at column from nota_dinas table
        if (Schema::hasColumn('nota_dinas', 'deleted_at')) {
            Schema::table('nota_dinas', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }

        // Remove deleted_at column from spt table
        if (Schema::hasColumn('spt', 'deleted_at')) {
            Schema::table('spt', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }

        // Remove deleted_at column from sppd table
        if (Schema::hasColumn('sppd', 'deleted_at')) {
            Schema::table('sppd', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }

        // Remove deleted_at column from receipts table
        if (Schema::hasColumn('receipts', 'deleted_at')) {
            Schema::table('receipts', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }

        // Remove deleted_at column from trip_reports table
        if (Schema::hasColumn('trip_reports', 'deleted_at')) {
            Schema::table('trip_reports', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add deleted_at column back to nota_dinas table
        if (!Schema::hasColumn('nota_dinas', 'deleted_at')) {
            Schema::table('nota_dinas', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add deleted_at column back to spt table
        if (!Schema::hasColumn('spt', 'deleted_at')) {
            Schema::table('spt', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add deleted_at column back to sppd table
        if (!Schema::hasColumn('sppd', 'deleted_at')) {
            Schema::table('sppd', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add deleted_at column back to receipts table
        if (!Schema::hasColumn('receipts', 'deleted_at')) {
            Schema::table('receipts', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add deleted_at column back to trip_reports table
        if (!Schema::hasColumn('trip_reports', 'deleted_at')) {
            Schema::table('trip_reports', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }
};

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
        Schema::table('trip_reports', function (Blueprint $table) {
            // Drop the unique constraint first
            $table->dropUnique(['doc_no']);
            
            // Make doc_no nullable
            $table->string('doc_no')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_reports', function (Blueprint $table) {
            // Make doc_no not nullable again
            $table->string('doc_no')->nullable(false)->change();
            
            // Add back the unique constraint
            $table->unique('doc_no');
        });
    }
};

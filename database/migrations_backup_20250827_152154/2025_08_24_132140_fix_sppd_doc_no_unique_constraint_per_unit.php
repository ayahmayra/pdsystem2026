<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            // Drop the global unique constraint on doc_no
            $table->dropUnique(['doc_no']);
            
            // Add unique constraint per unit (doc_no + number_scope_unit_id)
            $table->unique(['doc_no', 'number_scope_unit_id'], 'sppd_doc_no_unit_unique');
        });
    }

    public function down(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            // Restore the global unique constraint
            $table->dropUnique('sppd_doc_no_unit_unique');
            $table->unique(['doc_no'], 'sppd_doc_no_unique');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_numbers', function (Blueprint $table) {
            // Drop the global unique constraint on number
            $table->dropUnique(['number']);
            
            // Add unique constraint per doc_type (global scope for ND/SPT/LAP)
            // For SPPD/KWT, we'll handle uniqueness in the actual tables
            $table->unique(['number', 'doc_type'], 'document_numbers_number_doc_type_unique');
        });
    }

    public function down(): void
    {
        Schema::table('document_numbers', function (Blueprint $table) {
            // Restore the global unique constraint
            $table->dropUnique('document_numbers_number_doc_type_unique');
            $table->unique(['number'], 'document_numbers_number_unique');
        });
    }
};

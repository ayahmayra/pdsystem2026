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
        Schema::table('supporting_documents', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['trip_report_id']);
            $table->dropColumn('trip_report_id');
            
            // Add new foreign key to nota_dinas
            $table->foreignId('nota_dinas_id')->constrained('nota_dinas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supporting_documents', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['nota_dinas_id']);
            $table->dropColumn('nota_dinas_id');
            
            // Add back the original foreign key
            $table->foreignId('trip_report_id')->constrained('trip_reports')->onDelete('cascade');
        });
    }
};

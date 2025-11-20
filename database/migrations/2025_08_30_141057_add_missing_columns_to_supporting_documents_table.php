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
            // Add missing columns
            $table->string('title')->after('document_type'); // Judul dokumen
            $table->unsignedBigInteger('uploaded_by_user_id')->after('mime_type'); // User yang upload
            $table->boolean('is_active')->default(true)->after('uploaded_by_user_id'); // Status aktif
            
            // Add foreign key constraint for uploaded_by_user_id
            $table->foreign('uploaded_by_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supporting_documents', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['uploaded_by_user_id']);
            
            // Drop columns
            $table->dropColumn(['title', 'uploaded_by_user_id', 'is_active']);
        });
    }
};

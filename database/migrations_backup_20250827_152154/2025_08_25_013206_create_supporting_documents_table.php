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
        Schema::create('supporting_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_report_id')->constrained('trip_reports')->onDelete('cascade');
            $table->string('document_type'); // UNDANGAN, FOTO, DOKUMEN_LAIN, dll
            $table->string('title'); // Judul dokumen
            $table->text('description')->nullable(); // Deskripsi dokumen
            $table->string('file_path'); // Path file yang diupload
            $table->string('file_name'); // Nama asli file
            $table->string('file_size'); // Ukuran file
            $table->string('mime_type'); // Tipe MIME file
            $table->foreignId('uploaded_by_user_id')->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supporting_documents');
    }
};

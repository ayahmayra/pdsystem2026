<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('doc_type')->index();
            $table->unsignedBigInteger('doc_id')->index();
            $table->string('number')->unique(); // UNIQUE per doc_type (bisa pakai composite unique jika perlu)
            $table->foreignId('generated_by_user_id')->constrained('users');
            $table->boolean('is_manual')->default(false);
            $table->string('old_number')->nullable();
            $table->foreignId('format_id')->nullable()->constrained('doc_number_formats')->nullOnDelete();
            $table->foreignId('sequence_id')->nullable()->constrained('number_sequences')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_numbers');
    }
};

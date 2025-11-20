<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->unique();
            $table->boolean('number_is_manual')->default(false);
            $table->text('number_manual_reason')->nullable();
            $table->foreignId('number_format_id')->nullable()->constrained('doc_number_formats')->nullOnDelete();
            $table->foreignId('number_sequence_id')->nullable()->constrained('number_sequences')->nullOnDelete();
            $table->foreignId('number_scope_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('sppd_id')->constrained('sppd');
            $table->foreignId('travel_grade_id')->constrained('travel_grades');
            $table->string('receipt_no')->nullable();
            $table->date('receipt_date')->nullable();
            $table->foreignId('payee_user_id')->constrained('users');
            $table->decimal('total_amount', 16, 2);
            $table->text('notes')->nullable();
            $table->enum('status', ['DRAFT','FINAL']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_dinas', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->unique(); // nomor otomatis (boleh override)
            $table->boolean('number_is_manual')->default(false);
            $table->text('number_manual_reason')->nullable();
            $table->foreignId('number_format_id')->nullable()->constrained('doc_number_formats')->nullOnDelete();
            $table->foreignId('number_sequence_id')->nullable()->constrained('number_sequences')->nullOnDelete();
            $table->foreignId('number_scope_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('to_user_id')->constrained('users');
            $table->foreignId('from_user_id')->constrained('users');
            $table->text('tembusan')->nullable();
            $table->date('nd_date');
            $table->string('sifat')->nullable(); // dropdown di UI
            $table->integer('lampiran_count');
            $table->string('hal');
            $table->text('dasar');
            $table->text('maksud');
            $table->foreignId('destination_city_id')->constrained('cities');
            $table->date('start_date');
            $table->date('end_date');
            $table->smallInteger('days_count');
            $table->foreignId('requesting_unit_id')->constrained('units');
            $table->enum('status', ['DRAFT','SUBMITTED','APPROVED','REJECTED']);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_dinas');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spt', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->unique();
            $table->boolean('number_is_manual')->default(false);
            $table->text('number_manual_reason')->nullable();
            $table->foreignId('number_format_id')->nullable()->constrained('doc_number_formats')->nullOnDelete();
            $table->foreignId('number_sequence_id')->nullable()->constrained('number_sequences')->nullOnDelete();
            $table->foreignId('number_scope_unit_id')->nullable()->constrained('units')->nullOnDelete(); // biarkan NULL (global)
            $table->date('spt_date');
            $table->foreignId('nota_dinas_id')->constrained('nota_dinas');
            $table->foreignId('signed_by_user_id')->constrained('users');
            $table->string('assignment_title');
            $table->foreignId('origin_place_id')->constrained('org_places');
            $table->foreignId('destination_city_id')->constrained('cities');
            $table->date('start_date');
            $table->date('end_date');
            $table->smallInteger('days_count');
            $table->string('funding_source')->nullable();
            $table->enum('status', ['DRAFT','SIGNED','CANCELLED']);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spt');
    }
};

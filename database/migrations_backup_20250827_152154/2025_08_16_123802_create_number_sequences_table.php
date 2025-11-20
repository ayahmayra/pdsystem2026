<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('number_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('doc_type')->index();
            $table->foreignId('unit_scope_id')->nullable()->constrained('units')->nullOnDelete();
            $table->unsignedSmallInteger('year_scope')->nullable()->index();
            $table->unsignedTinyInteger('month_scope')->nullable()->index();
            $table->unsignedBigInteger('current_value');
            $table->timestamp('last_generated_at')->nullable();
            $table->timestamps();
            $table->unique(['doc_type', 'unit_scope_id', 'year_scope', 'month_scope'], 'uniq_numseq_doc_unit_year_month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('number_sequences');
    }
};

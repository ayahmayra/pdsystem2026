<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doc_number_formats', function (Blueprint $table) {
            $table->id();
            $table->string('doc_type')->index(); // ND/SPT/SPPD/KWT/LAP
            $table->foreignId('unit_scope_id')->nullable()->constrained('units')->nullOnDelete(); // null=global
            $table->string('format_string'); // contoh: {seq}/{doc_code}/{unit_code}/{roman_month}/{year}
            $table->string('doc_code');
            $table->enum('reset_policy', ['NEVER','YEARLY','MONTHLY']);
            $table->unsignedTinyInteger('padding')->default(3);
            $table->boolean('is_active')->default(true);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doc_number_formats');
    }
};

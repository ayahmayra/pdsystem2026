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
        Schema::table('receipts', function (Blueprint $table) {
            // Ensure all numbering-related fields are nullable for manual numbering
            $table->unsignedBigInteger('number_format_id')->nullable()->change();
            $table->unsignedBigInteger('number_sequence_id')->nullable()->change();
            $table->unsignedBigInteger('number_scope_unit_id')->nullable()->change();
            $table->string('number_manual_reason')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // Make fields not nullable again
            $table->unsignedBigInteger('number_format_id')->nullable(false)->change();
            $table->unsignedBigInteger('number_sequence_id')->nullable(false)->change();
            $table->unsignedBigInteger('number_scope_unit_id')->nullable(false)->change();
            $table->string('number_manual_reason')->nullable(false)->change();
        });
    }
};

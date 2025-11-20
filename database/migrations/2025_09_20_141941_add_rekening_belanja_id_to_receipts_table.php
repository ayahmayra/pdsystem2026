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
            $table->unsignedBigInteger('rekening_belanja_id')->nullable()->after('payee_user_id');
            $table->foreign('rekening_belanja_id')->references('id')->on('rekening_belanja')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropForeign(['rekening_belanja_id']);
            $table->dropColumn('rekening_belanja_id');
        });
    }
};
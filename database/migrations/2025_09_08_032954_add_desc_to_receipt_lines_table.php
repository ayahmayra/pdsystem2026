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
        Schema::table('receipt_lines', function (Blueprint $table) {
            $table->text('desc')->nullable()->after('remark')->comment('Keterangan tambahan untuk rincian biaya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_lines', function (Blueprint $table) {
            $table->dropColumn('desc');
        });
    }
};

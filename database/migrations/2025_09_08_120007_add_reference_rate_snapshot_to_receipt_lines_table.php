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
            $table->decimal('reference_rate_snapshot', 15, 2)->nullable()->after('cap_amount')
                ->comment('Snapshot of reference rate at time of receipt creation for lodging lines');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_lines', function (Blueprint $table) {
            $table->dropColumn('reference_rate_snapshot');
        });
    }
};

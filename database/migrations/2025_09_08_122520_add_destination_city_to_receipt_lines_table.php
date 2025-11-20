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
            $table->unsignedBigInteger('destination_city_id')->nullable()->after('reference_rate_snapshot')
                ->comment('City ID for lodging destination (for transit/multiple destinations)');
            $table->foreign('destination_city_id')->references('id')->on('cities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipt_lines', function (Blueprint $table) {
            $table->dropForeign(['destination_city_id']);
            $table->dropColumn('destination_city_id');
        });
    }
};

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
        Schema::table('sppd', function (Blueprint $table) {
            if (Schema::hasColumn('sppd', 'destination_city_id')) {
                $table->dropConstrainedForeignId('destination_city_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            if (!Schema::hasColumn('sppd', 'destination_city_id')) {
                $table->foreignId('destination_city_id')->nullable()->constrained('cities')->nullOnDelete();
            }
        });
    }
};

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
            if (Schema::hasColumn('sppd', 'origin_place_id')) {
                $table->dropConstrainedForeignId('origin_place_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            if (!Schema::hasColumn('sppd', 'origin_place_id')) {
                $table->foreignId('origin_place_id')->nullable()->constrained('org_places')->nullOnDelete();
            }
        });
    }
};

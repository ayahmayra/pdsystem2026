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
        Schema::table('spt', function (Blueprint $table) {
            // Hapus field yang duplikat dengan nota_dinas
            if (Schema::hasColumn('spt', 'origin_place_id')) {
                $table->dropForeign(['origin_place_id']);
                $table->dropColumn('origin_place_id');
            }
            if (Schema::hasColumn('spt', 'destination_city_id')) {
                $table->dropForeign(['destination_city_id']);
                $table->dropColumn('destination_city_id');
            }
            if (Schema::hasColumn('spt', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('spt', 'end_date')) {
                $table->dropColumn('end_date');
            }
            if (Schema::hasColumn('spt', 'days_count')) {
                $table->dropColumn('days_count');
            }
            if (Schema::hasColumn('spt', 'funding_source')) {
                $table->dropColumn('funding_source');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spt', function (Blueprint $table) {
            // Kembalikan field yang dihapus
            if (!Schema::hasColumn('spt', 'origin_place_id')) {
                $table->foreignId('origin_place_id')->constrained('org_places');
            }
            if (!Schema::hasColumn('spt', 'destination_city_id')) {
                $table->foreignId('destination_city_id')->constrained('cities');
            }
            if (!Schema::hasColumn('spt', 'start_date')) {
                $table->date('start_date');
            }
            if (!Schema::hasColumn('spt', 'end_date')) {
                $table->date('end_date');
            }
            if (!Schema::hasColumn('spt', 'days_count')) {
                $table->smallInteger('days_count')->default(1);
            }
            if (!Schema::hasColumn('spt', 'funding_source')) {
                $table->string('funding_source')->nullable();
            }
        });
    }
};

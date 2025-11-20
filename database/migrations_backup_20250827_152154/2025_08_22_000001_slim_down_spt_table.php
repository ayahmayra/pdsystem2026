<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spt', function (Blueprint $table) {
            // Drop FK constraints terlebih dahulu
            if (Schema::hasColumn('spt', 'origin_place_id')) {
                try { $table->dropForeign(['origin_place_id']); } catch (\Throwable $e) {}
            }
            if (Schema::hasColumn('spt', 'destination_city_id')) {
                try { $table->dropForeign(['destination_city_id']); } catch (\Throwable $e) {}
            }

            // Hapus kolom yang tidak diperlukan lagi
            $drop = [
                'origin_place_id', 'destination_city_id', 'start_date', 'end_date', 'days_count', 'funding_source',
            ];
            foreach ($drop as $col) {
                if (Schema::hasColumn('spt', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('spt', function (Blueprint $table) {
            // Kembalikan kolom yang dihapus (nullable agar aman)
            if (!Schema::hasColumn('spt', 'origin_place_id')) $table->foreignId('origin_place_id')->nullable()->constrained('org_places');
            if (!Schema::hasColumn('spt', 'destination_city_id')) $table->foreignId('destination_city_id')->nullable()->constrained('cities');
            if (!Schema::hasColumn('spt', 'start_date')) $table->date('start_date')->nullable();
            if (!Schema::hasColumn('spt', 'end_date')) $table->date('end_date')->nullable();
            if (!Schema::hasColumn('spt', 'days_count')) $table->smallInteger('days_count')->nullable();
            if (!Schema::hasColumn('spt', 'funding_source')) $table->string('funding_source')->nullable();
        });
    }
};

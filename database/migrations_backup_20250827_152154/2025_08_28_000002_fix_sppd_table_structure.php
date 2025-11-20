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
            // Hapus field yang tidak diperlukan
            if (Schema::hasColumn('sppd', 'days_count')) {
                $table->dropColumn('days_count');
            }
            if (Schema::hasColumn('sppd', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('sppd', 'end_date')) {
                $table->dropColumn('end_date');
            }
            if (Schema::hasColumn('sppd', 'origin_place_id')) {
                $table->dropForeign(['origin_place_id']);
                $table->dropColumn('origin_place_id');
            }
            if (Schema::hasColumn('sppd', 'destination_city_id')) {
                $table->dropForeign(['destination_city_id']);
                $table->dropColumn('destination_city_id');
            }
            if (Schema::hasColumn('sppd', 'transport_mode_id')) {
                $table->dropForeign(['transport_mode_id']);
                $table->dropColumn('transport_mode_id');
            }
            if (Schema::hasColumn('sppd', 'status')) {
                $table->dropColumn('status');
            }
            
            // Tambah field yang diperlukan
            if (!Schema::hasColumn('sppd', 'signed_by_user_id')) {
                $table->foreignId('signed_by_user_id')->nullable()->after('spt_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('sppd', 'assignment_title')) {
                $table->text('assignment_title')->nullable()->after('signed_by_user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            // Kembalikan field yang dihapus
            if (!Schema::hasColumn('sppd', 'days_count')) {
                $table->smallInteger('days_count')->default(1);
            }
            if (!Schema::hasColumn('sppd', 'start_date')) {
                $table->date('start_date');
            }
            if (!Schema::hasColumn('sppd', 'end_date')) {
                $table->date('end_date');
            }
            if (!Schema::hasColumn('sppd', 'origin_place_id')) {
                $table->foreignId('origin_place_id')->constrained('org_places');
            }
            if (!Schema::hasColumn('sppd', 'destination_city_id')) {
                $table->foreignId('destination_city_id')->constrained('cities');
            }
            if (!Schema::hasColumn('sppd', 'transport_mode_id')) {
                $table->foreignId('transport_mode_id')->constrained('transport_modes');
            }
            if (!Schema::hasColumn('sppd', 'status')) {
                $table->enum('status', ['DRAFT','ISSUED','IN_TRAVEL','RETURNED','VERIFIED','PAID','CANCELLED'])->default('DRAFT');
            }
            
            // Hapus field yang ditambahkan
            if (Schema::hasColumn('sppd', 'signed_by_user_id')) {
                $table->dropConstrainedForeignId('signed_by_user_id');
            }
            if (Schema::hasColumn('sppd', 'assignment_title')) {
                $table->dropColumn('assignment_title');
            }
        });
    }
};

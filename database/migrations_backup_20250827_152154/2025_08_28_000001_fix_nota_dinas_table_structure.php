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
        Schema::table('nota_dinas', function (Blueprint $table) {
            // Hapus field yang tidak diperlukan
            if (Schema::hasColumn('nota_dinas', 'days_count')) {
                $table->dropColumn('days_count');
            }
            if (Schema::hasColumn('nota_dinas', 'spt_request_date')) {
                $table->dropColumn('spt_request_date');
            }
            if (Schema::hasColumn('nota_dinas', 'signer_user_id')) {
                $table->dropForeign(['signer_user_id']);
                $table->dropColumn('signer_user_id');
            }
            
            // Tambah field yang diperlukan
            if (!Schema::hasColumn('nota_dinas', 'origin_place_id')) {
                $table->foreignId('origin_place_id')->nullable()->after('destination_city_id')->constrained('org_places')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            // Kembalikan field yang dihapus
            if (!Schema::hasColumn('nota_dinas', 'days_count')) {
                $table->smallInteger('days_count')->default(1);
            }
            if (!Schema::hasColumn('nota_dinas', 'spt_request_date')) {
                $table->date('spt_request_date')->nullable();
            }
            if (!Schema::hasColumn('nota_dinas', 'signer_user_id')) {
                $table->foreignId('signer_user_id')->nullable()->constrained('users')->nullOnDelete();
            }
            
            // Hapus field yang ditambahkan
            if (Schema::hasColumn('nota_dinas', 'origin_place_id')) {
                $table->dropConstrainedForeignId('origin_place_id');
            }
        });
    }
};

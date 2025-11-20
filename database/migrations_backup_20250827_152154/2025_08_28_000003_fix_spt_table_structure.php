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
            // Hapus field yang tidak diperlukan
            if (Schema::hasColumn('spt', 'status')) {
                $table->dropColumn('status');
            }
            
            // Ubah tipe data assignment_title menjadi text
            if (Schema::hasColumn('spt', 'assignment_title')) {
                $table->text('assignment_title')->change();
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
            if (!Schema::hasColumn('spt', 'status')) {
                $table->enum('status', ['DRAFT','SIGNED','CANCELLED'])->default('DRAFT');
            }
            
            // Kembalikan tipe data assignment_title
            if (Schema::hasColumn('spt', 'assignment_title')) {
                $table->string('assignment_title')->change();
            }
        });
    }
};

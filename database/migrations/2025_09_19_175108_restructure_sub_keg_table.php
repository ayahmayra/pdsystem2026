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
        Schema::table('sub_keg', function (Blueprint $table) {
            // Drop pagu column
            $table->dropColumn('pagu');
            
            // Make id_unit nullable
            $table->unsignedBigInteger('id_unit')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_keg', function (Blueprint $table) {
            // Add back pagu column
            $table->decimal('pagu', 15, 2)->nullable()->after('nama_subkeg');
            
            // Make id_unit not nullable again
            $table->unsignedBigInteger('id_unit')->nullable(false)->change();
        });
    }
};

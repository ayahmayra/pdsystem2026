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
            $table->decimal('pagu', 15, 2)->nullable()->after('nama_subkeg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_keg', function (Blueprint $table) {
            $table->dropColumn('pagu');
        });
    }
};

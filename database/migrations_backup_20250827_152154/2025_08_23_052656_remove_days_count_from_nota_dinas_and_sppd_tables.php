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
            $table->dropColumn('days_count');
        });

        Schema::table('sppd', function (Blueprint $table) {
            $table->dropColumn('days_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->integer('days_count')->default(1);
        });

        Schema::table('sppd', function (Blueprint $table) {
            $table->integer('days_count')->default(1);
        });
    }
};

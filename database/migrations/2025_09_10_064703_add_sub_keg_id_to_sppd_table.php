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
            $table->unsignedBigInteger('sub_keg_id')->nullable()->after('pptk_user_id');
            $table->foreign('sub_keg_id')->references('id')->on('sub_keg')->onDelete('set null');
            $table->index('sub_keg_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            $table->dropForeign(['sub_keg_id']);
            $table->dropIndex(['sub_keg_id']);
            $table->dropColumn('sub_keg_id');
        });
    }
};

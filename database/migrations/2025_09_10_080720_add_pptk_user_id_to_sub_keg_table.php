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
            $table->unsignedBigInteger('pptk_user_id')->nullable()->after('id_unit');
            $table->foreign('pptk_user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('pptk_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_keg', function (Blueprint $table) {
            $table->dropForeign(['pptk_user_id']);
            $table->dropIndex(['pptk_user_id']);
            $table->dropColumn('pptk_user_id');
        });
    }
};

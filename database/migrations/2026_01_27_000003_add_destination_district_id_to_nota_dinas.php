<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add destination_district_id to nota_dinas table to support
     * selecting districts (kecamatan) as destination for intra-district trips.
     */
    public function up(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->unsignedBigInteger('destination_district_id')->nullable()->after('destination_city_id');
            $table->foreign('destination_district_id')->references('id')->on('districts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->dropForeign(['destination_district_id']);
            $table->dropColumn('destination_district_id');
        });
    }
};

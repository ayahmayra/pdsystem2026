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
            $table->dropForeign(['travel_grade_id_snapshot']);
            $table->dropColumn([
                'travel_grade_id_snapshot',
                'travel_grade_code_snapshot',
                'travel_grade_name_snapshot'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->unsignedBigInteger('travel_grade_id_snapshot')->nullable()->after('custom_signer_title');
            $table->string('travel_grade_code_snapshot')->nullable()->after('travel_grade_id_snapshot');
            $table->string('travel_grade_name_snapshot')->nullable()->after('travel_grade_code_snapshot');
            $table->foreign('travel_grade_id_snapshot')->references('id')->on('travel_grades')->nullOnDelete();
        });
    }
};

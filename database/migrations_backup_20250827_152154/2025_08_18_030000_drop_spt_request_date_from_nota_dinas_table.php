<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            if (Schema::hasColumn('nota_dinas', 'spt_request_date')) {
                $table->dropColumn('spt_request_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->date('spt_request_date')->nullable();
        });
    }
};

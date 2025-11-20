<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('spt', 'status')) {
            Schema::table('spt', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('spt', 'status')) {
            Schema::table('spt', function (Blueprint $table) {
                $table->enum('status', ['DRAFT','SIGNED','CANCELLED'])->default('DRAFT');
            });
        }
    }
};



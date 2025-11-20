<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            $table->foreignId('signed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('assignment_title')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sppd', function (Blueprint $table) {
            $table->dropConstrainedForeignId('signed_by_user_id');
            $table->dropColumn('assignment_title');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            if (Schema::hasColumn('nota_dinas', 'signer_user_id')) {
                $table->dropForeign(['signer_user_id']);
                $table->dropColumn('signer_user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->foreignId('signer_user_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }
};

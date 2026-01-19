<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('employee_type', ['PNS', 'PPPK', 'PPPK PW', 'Non ASN'])
                  ->default('PNS')
                  ->after('email')
                  ->comment('Jenis Pegawai: PNS, PPPK, PPPK PW, Non ASN');
        });

        // Update existing users to have default employee_type 'PNS'
        DB::table('users')->whereNull('employee_type')->update(['employee_type' => 'PNS']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('employee_type');
        });
    }
};

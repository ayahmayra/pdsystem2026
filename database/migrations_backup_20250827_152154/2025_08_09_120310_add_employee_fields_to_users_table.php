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
        Schema::table('users', function (Blueprint $table) {
            // Identitas Pegawai
            $table->string('nip', 20)->nullable()->unique();
            $table->string('nik', 20)->nullable()->unique();
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('address')->nullable();

            // Relasi ke referensi
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('rank_id')->nullable()->constrained('ranks')->nullOnDelete();
            $table->foreignId('echelon_id')->nullable()->constrained('echelons')->nullOnDelete();

            // Data keuangan
            $table->string('npwp', 25)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_no', 50)->nullable();
            $table->string('bank_account_name')->nullable();

            // Lainnya
            $table->date('birth_date')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('signature_path')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('is_signer')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nip','nik','gelar_depan','gelar_belakang','phone','whatsapp','address',
                'npwp','bank_name','bank_account_no','bank_account_name',
                'birth_date','gender','signature_path','photo_path','is_signer'
            ]);
            $table->dropConstrainedForeignId('unit_id');
            $table->dropConstrainedForeignId('position_id');
            $table->dropConstrainedForeignId('rank_id');
            $table->dropConstrainedForeignId('echelon_id');
        });
    }
};

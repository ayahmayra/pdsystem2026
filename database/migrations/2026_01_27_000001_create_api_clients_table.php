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
        Schema::create('api_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama aplikasi/client
            $table->string('api_key', 64)->unique(); // API Key (hashed)
            $table->text('description')->nullable(); // Deskripsi aplikasi
            $table->string('ip_whitelist')->nullable(); // IP whitelist (comma-separated)
            $table->boolean('is_active')->default(true); // Status aktif/nonaktif
            $table->timestamp('last_used_at')->nullable(); // Terakhir digunakan
            $table->integer('request_count')->default(0); // Jumlah request
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_clients');
    }
};

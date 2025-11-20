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
        Schema::create('org_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            $table->foreignId('head_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('head_title')->default('Kepala Dinas');
            $table->string('signature_path')->nullable();
            $table->string('stamp_path')->nullable();

            $table->json('settings')->nullable();
            $table->boolean('singleton')->default(true);
            $table->timestamps();
            $table->unique('singleton');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('org_settings');
    }
};

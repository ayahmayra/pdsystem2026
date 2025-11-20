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
            $table->string('short_name', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->unsignedBigInteger('head_user_id')->nullable();
            $table->string('head_title', 100);
            $table->string('signature_path')->nullable();
            $table->string('stamp_path')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('ym_separator', 5)->default('/');
            $table->string('qr_footer_text')->nullable();
            $table->boolean('show_left_logo')->default(true);
            $table->boolean('show_right_logo')->default(false);
            $table->boolean('singleton')->default(true);
            $table->timestamps();

            $table->foreign('head_user_id')->references('id')->on('users')->onDelete('set null');
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

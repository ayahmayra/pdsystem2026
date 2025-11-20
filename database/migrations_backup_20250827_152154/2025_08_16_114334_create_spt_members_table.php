<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spt_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spt_id')->constrained('spt')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
            $table->unique(['spt_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spt_members');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('spt_members')) {
            Schema::drop('spt_members');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('spt_members')) {
            Schema::create('spt_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('spt_id')->constrained('spt')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users');
                $table->timestamps();
                $table->unique(['spt_id','user_id']);
            });
        }
    }
};



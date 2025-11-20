<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_dinas_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_dinas_id')->constrained('nota_dinas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->string('role_in_trip')->nullable();
            $table->timestamps();
            $table->unique(['nota_dinas_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_dinas_participants');
    }
};

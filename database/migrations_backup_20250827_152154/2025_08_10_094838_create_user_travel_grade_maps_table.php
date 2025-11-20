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
        Schema::create('user_travel_grade_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('travel_grade_id')->constrained('travel_grades')->cascadeOnDelete();
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi mapping
            $table->unique('user_id');
            
            // Indexes untuk performa
            $table->index('user_id');
            $table->index('travel_grade_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_travel_grade_maps');
    }
};

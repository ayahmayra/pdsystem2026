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
        Schema::create('lodging_caps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete();
            $table->foreignId('travel_grade_id')->constrained('travel_grades')->cascadeOnDelete();
            $table->decimal('cap_amount', 12, 2); // Batas maksimal tarif penginapan
            $table->timestamps();
            
            // Unique constraint untuk kombinasi (province_id, travel_grade_id)
            $table->unique(['province_id', 'travel_grade_id'], 'unique_lodging_province_grade');
            
            // Indexes untuk performa
            $table->index('province_id');
            $table->index('travel_grade_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lodging_caps');
    }
};

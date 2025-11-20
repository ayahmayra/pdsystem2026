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
        Schema::create('perdiem_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete();
            $table->foreignId('travel_grade_id')->constrained('travel_grades')->cascadeOnDelete();
            $table->string('satuan', 10)->default('OH');
            $table->decimal('luar_kota', 12, 2); // Tarif untuk perjalanan luar kota
            $table->decimal('dalam_kota_gt8h', 12, 2); // Tarif untuk dalam kota > 8 jam
            $table->decimal('diklat', 12, 2); // Tarif untuk diklat
            $table->timestamps();
            
            // Unique constraint untuk kombinasi (province_id, travel_grade_id)
            $table->unique(['province_id', 'travel_grade_id'], 'unique_perdiem_province_grade');
            
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
        Schema::dropIfExists('perdiem_rates');
    }
};

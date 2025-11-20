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
        Schema::create('representation_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_grade_id')->constrained('travel_grades')->cascadeOnDelete();
            $table->string('satuan', 10)->default('OH');
            $table->decimal('luar_kota', 12, 2); // Tarif representasi untuk perjalanan luar kota
            $table->decimal('dalam_kota_gt8h', 12, 2); // Tarif representasi untuk dalam kota > 8 jam
            $table->timestamps();
            
            // Unique constraint untuk travel_grade_id (satu tingkatan hanya bisa ada satu tarif representasi)
            $table->unique('travel_grade_id', 'unique_representation_travel_grade');
            
            // Index untuk performa
            $table->index('travel_grade_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('representation_rates');
    }
};

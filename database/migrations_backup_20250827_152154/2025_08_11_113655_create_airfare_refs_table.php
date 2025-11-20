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
        Schema::create('airfare_refs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_city_id')->constrained('cities')->cascadeOnDelete();
            $table->foreignId('destination_city_id')->constrained('cities')->cascadeOnDelete();
            $table->enum('class', ['ECONOMY', 'BUSINESS'])->default('ECONOMY');
            $table->decimal('pp_estimate', 12, 2); // Per person estimate untuk RAB
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi rute
            $table->unique(['origin_city_id', 'destination_city_id', 'class'], 'unique_airfare_route_class');
            $table->index('origin_city_id');
            $table->index('destination_city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airfare_refs');
    }
};

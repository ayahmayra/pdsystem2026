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
        Schema::create('travel_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_place_id')->constrained('org_places')->cascadeOnDelete();
            $table->foreignId('destination_place_id')->constrained('org_places')->cascadeOnDelete();
            $table->foreignId('mode_id')->constrained('transport_modes')->cascadeOnDelete();
            $table->boolean('is_roundtrip')->default(false);
            $table->enum('class', ['ECONOMY', 'BUSINESS'])->nullable();
            $table->timestamps();
            
            // Unique constraint untuk kombinasi (origin_place_id, destination_place_id, mode_id, class)
            $table->unique(['origin_place_id', 'destination_place_id', 'mode_id', 'class'], 'unique_route_combination');
            
            // Indexes untuk performa
            $table->index('origin_place_id');
            $table->index('destination_place_id');
            $table->index('mode_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_routes');
    }
};

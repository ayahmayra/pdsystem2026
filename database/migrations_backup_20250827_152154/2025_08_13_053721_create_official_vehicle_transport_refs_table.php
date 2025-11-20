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
        Schema::create('official_vehicle_transport_refs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_place_id')->constrained('org_places')->onDelete('cascade');
            $table->foreignId('destination_district_id')->constrained('districts')->onDelete('cascade');
            $table->decimal('pp_amount', 12, 2); // Tarif per orang
            $table->string('context'); // Kedudukan Bengkalis / Kedudukan Duri
            $table->timestamps();

            // Unique constraint untuk kombinasi origin_place, destination_district, dan context
            $table->unique(['origin_place_id', 'destination_district_id', 'context'], 'unique_official_vehicle_transport');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_vehicle_transport_refs');
    }
};

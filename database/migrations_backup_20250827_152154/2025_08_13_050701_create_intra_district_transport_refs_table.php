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
        Schema::create('intra_district_transport_refs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_place_id')->constrained('org_places')->onDelete('cascade');
            $table->foreignId('destination_district_id')->constrained('districts')->onDelete('cascade');
            $table->decimal('pp_amount', 12, 2); // Tarif per orang
            $table->timestamps();

            // Unique constraint untuk kombinasi origin_place dan destination_district
            $table->unique(['origin_place_id', 'destination_district_id'], 'unique_intra_district_transport');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intra_district_transport_refs');
    }
};

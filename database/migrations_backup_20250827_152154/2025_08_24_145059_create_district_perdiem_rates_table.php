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
        Schema::create('district_perdiem_rates', function (Blueprint $table) {
            $table->id();
            $table->string('org_place_name'); // Nama kedudukan (Bengkalis, Duri, dll)
            $table->unsignedBigInteger('district_id'); // ID kecamatan
            $table->string('unit', 10)->default('OH'); // Satuan (OH = Orang/Hari)
            $table->decimal('daily_rate', 12, 2); // Uang harian
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Foreign key ke tabel districts
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['org_place_name', 'district_id'], 'unique_org_place_district');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('district_perdiem_rates');
    }
};

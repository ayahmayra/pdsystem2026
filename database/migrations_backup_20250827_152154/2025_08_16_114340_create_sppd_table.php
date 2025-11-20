<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sppd', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->unique();
            $table->boolean('number_is_manual')->default(false);
            $table->text('number_manual_reason')->nullable();
            $table->foreignId('number_format_id')->nullable()->constrained('doc_number_formats')->nullOnDelete();
            $table->foreignId('number_sequence_id')->nullable()->constrained('number_sequences')->nullOnDelete();
            $table->foreignId('number_scope_unit_id')->constrained('units'); // WAJIB: isi dari nota_dinas.requesting_unit_id
            $table->date('sppd_date');
            $table->foreignId('spt_id')->constrained('spt');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('origin_place_id')->constrained('org_places');
            $table->foreignId('destination_city_id')->constrained('cities');
            $table->foreignId('transport_mode_id')->constrained('transport_modes');
            $table->enum('trip_type', ['LUAR_DAERAH','DALAM_DAERAH_GT8H','DALAM_DAERAH_LE8H','DIKLAT']);
            $table->date('start_date');
            $table->date('end_date');
            $table->smallInteger('days_count');
            $table->string('funding_source')->nullable();
            $table->enum('status', ['DRAFT','ISSUED','IN_TRAVEL','RETURNED','VERIFIED','PAID','CANCELLED']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sppd');
    }
};

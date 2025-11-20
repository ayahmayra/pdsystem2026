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
        Schema::create('rekening_belanja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_keg_id');
            $table->string('kode_rekening')->unique();
            $table->string('nama_rekening');
            $table->decimal('pagu', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('sub_keg_id')->references('id')->on('sub_keg')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('sub_keg_id');
            $table->index('kode_rekening');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekening_belanja');
    }
};

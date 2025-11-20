<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipt_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained('receipts')->onDelete('cascade');
            $table->enum('component', ['PERDIEM','REPRESENTASI','LODGING','AIRFARE','INTRA_PROV','INTRA_DISTRICT','OFFICIAL_VEHICLE','TAXI','RORO','TOLL','PARKIR_INAP','RAPID_TEST','LAINNYA']);
            $table->decimal('qty', 10, 2);
            $table->string('unit')->nullable();
            $table->decimal('unit_amount', 16, 2);
            $table->decimal('line_total', 16, 2);
            $table->string('ref_table')->nullable();
            $table->bigInteger('ref_id')->nullable();
            $table->decimal('cap_amount', 16, 2)->nullable();
            $table->boolean('is_over_cap')->default(false);
            $table->decimal('over_cap_amount', 16, 2)->nullable();
            $table->string('remark')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_lines');
    }
};

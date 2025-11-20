<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receipt_lines', function (Blueprint $table) {
            // Drop existing columns that are not needed
            $table->dropColumn([
                'ref_table',
                'ref_id', 
                'cap_amount',
                'is_over_cap',
                'over_cap_amount'
            ]);
            
            // Modify component enum to match new simplified structure
            $table->enum('component', [
                'TRANSPORT_LAUT',
                'TRANSPORT_DARAT', 
                'TRANSPORT_DARAT_RORO',
                'TRANSPORT_UDARA',
                'TRANSPORT_TAXI',
                'LODGING',
                'PERDIEM',
                'REPRESENTASI'
            ])->change();
            
            // Add new columns for simplified structure
            $table->boolean('is_no_lodging')->default(false)->after('line_total');
            $table->string('destination_city')->nullable()->after('is_no_lodging');
        });
    }

    public function down(): void
    {
        Schema::table('receipt_lines', function (Blueprint $table) {
            // Restore original columns
            $table->string('ref_table')->nullable();
            $table->bigInteger('ref_id')->nullable();
            $table->decimal('cap_amount', 16, 2)->nullable();
            $table->boolean('is_over_cap')->default(false);
            $table->decimal('over_cap_amount', 16, 2)->nullable();
            
            // Restore original component enum
            $table->enum('component', ['PERDIEM','REPRESENTASI','LODGING','AIRFARE','INTRA_PROV','INTRA_DISTRICT','OFFICIAL_VEHICLE','TAXI','RORO','TOLL','PARKIR_INAP','RAPID_TEST','LAINNYA'])->change();
            
            // Drop new columns
            $table->dropColumn(['is_no_lodging', 'destination_city']);
        });
    }
};

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
        // Add foreign key constraints for users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('unit_id')->references('id')->on('units')->nullOnDelete();
            $table->foreign('position_id')->references('id')->on('positions')->nullOnDelete();
            $table->foreign('rank_id')->references('id')->on('ranks')->nullOnDelete();
        });

        // Add foreign key constraints for units table
        Schema::table('units', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('units')->nullOnDelete();
        });

        // Add foreign key constraints for positions table
        Schema::table('positions', function (Blueprint $table) {
            $table->foreign('echelon_id')->references('id')->on('echelons')->nullOnDelete();
        });

        // Add foreign key constraints for cities table
        Schema::table('cities', function (Blueprint $table) {
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
        });

        // Add foreign key constraints for districts table
        Schema::table('districts', function (Blueprint $table) {
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
        });

        // Add foreign key constraints for org_places table
        Schema::table('org_places', function (Blueprint $table) {
            $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
        });

        // Add foreign key constraints for user_travel_grade_maps table
        Schema::table('user_travel_grade_maps', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('travel_grade_id')->references('id')->on('travel_grades')->onDelete('cascade');
        });

        // Add foreign key constraints for perdiem_rates table
        Schema::table('perdiem_rates', function (Blueprint $table) {
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
            $table->foreign('travel_grade_id')->references('id')->on('travel_grades')->onDelete('cascade');
        });

        // Add foreign key constraints for lodging_caps table
        Schema::table('lodging_caps', function (Blueprint $table) {
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
            $table->foreign('travel_grade_id')->references('id')->on('travel_grades')->onDelete('cascade');
        });

        // Add foreign key constraints for representation_rates table
        Schema::table('representation_rates', function (Blueprint $table) {
            $table->foreign('travel_grade_id')->references('id')->on('travel_grades')->onDelete('cascade');
        });

        // Add foreign key constraints for airfare_refs table
        Schema::table('airfare_refs', function (Blueprint $table) {
            $table->foreign('origin_city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('destination_city_id')->references('id')->on('cities')->onDelete('cascade');
        });

        // Add foreign key constraints for intra_province_transport_refs table
        Schema::table('intra_province_transport_refs', function (Blueprint $table) {
            $table->foreign('origin_place_id')->references('id')->on('org_places')->onDelete('cascade');
            $table->foreign('destination_city_id')->references('id')->on('cities')->onDelete('cascade');
        });

        // Add foreign key constraints for intra_district_transport_refs table
        Schema::table('intra_district_transport_refs', function (Blueprint $table) {
            $table->foreign('origin_place_id')->references('id')->on('org_places')->onDelete('cascade');
            $table->foreign('destination_district_id')->references('id')->on('districts')->onDelete('cascade');
        });

        // Add foreign key constraints for official_vehicle_transport_refs table
        Schema::table('official_vehicle_transport_refs', function (Blueprint $table) {
            $table->foreign('origin_place_id')->references('id')->on('org_places')->onDelete('cascade');
            $table->foreign('destination_district_id')->references('id')->on('districts')->onDelete('cascade');
        });

        // Add foreign key constraints for doc_number_formats table
        Schema::table('doc_number_formats', function (Blueprint $table) {
            $table->foreign('unit_scope_id')->references('id')->on('units')->nullOnDelete();
        });

        // Add foreign key constraints for number_sequences table
        Schema::table('number_sequences', function (Blueprint $table) {
            $table->foreign('unit_scope_id')->references('id')->on('units')->nullOnDelete();
        });

        // Add foreign key constraints for document_numbers table
        Schema::table('document_numbers', function (Blueprint $table) {
            $table->foreign('generated_by_user_id')->references('id')->on('users');
            $table->foreign('format_id')->references('id')->on('doc_number_formats')->nullOnDelete();
            $table->foreign('sequence_id')->references('id')->on('number_sequences')->nullOnDelete();
        });

        // Add foreign key constraints for org_settings table
        Schema::table('org_settings', function (Blueprint $table) {
            $table->foreign('head_user_id')->references('id')->on('users')->nullOnDelete();
        });

        // Add foreign key constraints for nota_dinas table
        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->foreign('number_format_id')->references('id')->on('doc_number_formats')->nullOnDelete();
            $table->foreign('number_sequence_id')->references('id')->on('number_sequences')->nullOnDelete();
            $table->foreign('number_scope_unit_id')->references('id')->on('units')->nullOnDelete();
            $table->foreign('to_user_id')->references('id')->on('users');
            $table->foreign('from_user_id')->references('id')->on('users');
            $table->foreign('destination_city_id')->references('id')->on('cities');
            $table->foreign('origin_place_id')->references('id')->on('org_places')->nullOnDelete();
            $table->foreign('requesting_unit_id')->references('id')->on('units');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });

        // Add foreign key constraints for nota_dinas_participants table
        Schema::table('nota_dinas_participants', function (Blueprint $table) {
            $table->foreign('nota_dinas_id')->references('id')->on('nota_dinas')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Add foreign key constraints for spt table
        Schema::table('spt', function (Blueprint $table) {
            $table->foreign('number_format_id')->references('id')->on('doc_number_formats')->nullOnDelete();
            $table->foreign('number_sequence_id')->references('id')->on('number_sequences')->nullOnDelete();
            $table->foreign('number_scope_unit_id')->references('id')->on('units')->nullOnDelete();
            $table->foreign('nota_dinas_id')->references('id')->on('nota_dinas');
            $table->foreign('signed_by_user_id')->references('id')->on('users');
        });

        // Add foreign key constraints for spt_members table
        Schema::table('spt_members', function (Blueprint $table) {
            $table->foreign('spt_id')->references('id')->on('spt')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Add foreign key constraints for sppd table
        Schema::table('sppd', function (Blueprint $table) {
            $table->foreign('number_format_id')->references('id')->on('doc_number_formats')->nullOnDelete();
            $table->foreign('number_sequence_id')->references('id')->on('number_sequences')->nullOnDelete();
            $table->foreign('number_scope_unit_id')->references('id')->on('units');
            $table->foreign('spt_id')->references('id')->on('spt');
            $table->foreign('signed_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users');
        });

        // Add foreign key constraints for sppd_transport_modes table
        Schema::table('sppd_transport_modes', function (Blueprint $table) {
            $table->foreign('sppd_id')->references('id')->on('sppd')->onDelete('cascade');
            $table->foreign('transport_mode_id')->references('id')->on('transport_modes')->onDelete('cascade');
        });

        // Add foreign key constraints for sppd_itineraries table
        Schema::table('sppd_itineraries', function (Blueprint $table) {
            $table->foreign('sppd_id')->references('id')->on('sppd')->onDelete('cascade');
        });

        // Add foreign key constraints for sppd_divisum_signoffs table
        Schema::table('sppd_divisum_signoffs', function (Blueprint $table) {
            $table->foreign('sppd_id')->references('id')->on('sppd')->onDelete('cascade');
        });

        // Add foreign key constraints for receipts table
        Schema::table('receipts', function (Blueprint $table) {
            $table->foreign('number_format_id')->references('id')->on('doc_number_formats')->nullOnDelete();
            $table->foreign('number_sequence_id')->references('id')->on('number_sequences')->nullOnDelete();
            $table->foreign('number_scope_unit_id')->references('id')->on('units')->nullOnDelete();
            $table->foreign('sppd_id')->references('id')->on('sppd');
            $table->foreign('travel_grade_id')->references('id')->on('travel_grades');
            $table->foreign('payee_user_id')->references('id')->on('users');
        });

        // Add foreign key constraints for receipt_lines table
        Schema::table('receipt_lines', function (Blueprint $table) {
            $table->foreign('receipt_id')->references('id')->on('receipts')->onDelete('cascade');
        });

        // Add foreign key constraints for trip_reports table
        Schema::table('trip_reports', function (Blueprint $table) {
            $table->foreign('number_format_id')->references('id')->on('doc_number_formats')->nullOnDelete();
            $table->foreign('number_sequence_id')->references('id')->on('number_sequences')->nullOnDelete();
            $table->foreign('number_scope_unit_id')->references('id')->on('units')->nullOnDelete();
            $table->foreign('spt_id')->references('id')->on('spt');
            $table->foreign('created_by_user_id')->references('id')->on('users');
        });

        // Add foreign key constraints for trip_report_signers table
        Schema::table('trip_report_signers', function (Blueprint $table) {
            $table->foreign('trip_report_id')->references('id')->on('trip_reports')->onDelete('cascade');
        });

        // Add foreign key constraints for supporting_documents table
        Schema::table('supporting_documents', function (Blueprint $table) {
            $table->foreign('nota_dinas_id')->references('id')->on('nota_dinas')->onDelete('cascade');
        });

        // Add foreign key constraints for district_perdiem_rates table
        Schema::table('district_perdiem_rates', function (Blueprint $table) {
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->foreign('travel_grade_id')->references('id')->on('travel_grades')->onDelete('cascade');
        });

        // Add foreign key constraints for travel_routes table
        Schema::table('travel_routes', function (Blueprint $table) {
            $table->foreign('origin_place_id')->references('id')->on('org_places')->onDelete('cascade');
            $table->foreign('destination_place_id')->references('id')->on('org_places')->onDelete('cascade');
            $table->foreign('mode_id')->references('id')->on('transport_modes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all foreign key constraints in reverse order
        Schema::table('travel_routes', function (Blueprint $table) {
            $table->dropForeign(['origin_place_id', 'destination_place_id', 'mode_id']);
        });

        Schema::table('district_perdiem_rates', function (Blueprint $table) {
            $table->dropForeign(['district_id', 'travel_grade_id']);
        });

        Schema::table('supporting_documents', function (Blueprint $table) {
            $table->dropForeign(['nota_dinas_id']);
        });

        Schema::table('trip_report_signers', function (Blueprint $table) {
            $table->dropForeign(['trip_report_id']);
        });

        Schema::table('trip_reports', function (Blueprint $table) {
            $table->dropForeign(['number_format_id', 'number_sequence_id', 'number_scope_unit_id', 'spt_id', 'created_by_user_id']);
        });

        Schema::table('receipt_lines', function (Blueprint $table) {
            $table->dropForeign(['receipt_id']);
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropForeign(['number_format_id', 'number_sequence_id', 'number_scope_unit_id', 'sppd_id', 'travel_grade_id', 'payee_user_id']);
        });

        Schema::table('sppd_divisum_signoffs', function (Blueprint $table) {
            $table->dropForeign(['sppd_id']);
        });

        Schema::table('sppd_itineraries', function (Blueprint $table) {
            $table->dropForeign(['sppd_id']);
        });

        Schema::table('sppd_transport_modes', function (Blueprint $table) {
            $table->dropForeign(['sppd_id', 'transport_mode_id']);
        });

        Schema::table('sppd', function (Blueprint $table) {
            $table->dropForeign(['number_format_id', 'number_sequence_id', 'number_scope_unit_id', 'spt_id', 'signed_by_user_id', 'user_id']);
        });

        Schema::table('spt_members', function (Blueprint $table) {
            $table->dropForeign(['spt_id', 'user_id']);
        });

        Schema::table('spt', function (Blueprint $table) {
            $table->dropForeign(['number_format_id', 'number_sequence_id', 'number_scope_unit_id', 'nota_dinas_id', 'signed_by_user_id']);
        });

        Schema::table('nota_dinas_participants', function (Blueprint $table) {
            $table->dropForeign(['nota_dinas_id', 'user_id']);
        });

        Schema::table('nota_dinas', function (Blueprint $table) {
            $table->dropForeign(['number_format_id', 'number_sequence_id', 'number_scope_unit_id', 'to_user_id', 'from_user_id', 'destination_city_id', 'origin_place_id', 'requesting_unit_id', 'created_by', 'approved_by']);
        });

        Schema::table('org_settings', function (Blueprint $table) {
            $table->dropForeign(['head_user_id']);
        });

        Schema::table('document_numbers', function (Blueprint $table) {
            $table->dropForeign(['generated_by_user_id', 'format_id', 'sequence_id']);
        });

        Schema::table('number_sequences', function (Blueprint $table) {
            $table->dropForeign(['unit_scope_id']);
        });

        Schema::table('doc_number_formats', function (Blueprint $table) {
            $table->dropForeign(['unit_scope_id']);
        });

        Schema::table('official_vehicle_transport_refs', function (Blueprint $table) {
            $table->dropForeign(['origin_place_id', 'destination_district_id']);
        });

        Schema::table('intra_district_transport_refs', function (Blueprint $table) {
            $table->dropForeign(['origin_place_id', 'destination_district_id']);
        });

        Schema::table('intra_province_transport_refs', function (Blueprint $table) {
            $table->dropForeign(['origin_place_id', 'destination_city_id']);
        });

        Schema::table('airfare_refs', function (Blueprint $table) {
            $table->dropForeign(['origin_city_id', 'destination_city_id']);
        });

        Schema::table('representation_rates', function (Blueprint $table) {
            $table->dropForeign(['travel_grade_id']);
        });

        Schema::table('lodging_caps', function (Blueprint $table) {
            $table->dropForeign(['province_id', 'travel_grade_id']);
        });

        Schema::table('perdiem_rates', function (Blueprint $table) {
            $table->dropForeign(['province_id', 'travel_grade_id']);
        });

        Schema::table('user_travel_grade_maps', function (Blueprint $table) {
            $table->dropForeign(['user_id', 'travel_grade_id']);
        });

        Schema::table('org_places', function (Blueprint $table) {
            $table->dropForeign(['city_id', 'district_id']);
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->dropForeign(['echelon_id']);
        });

        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['unit_id', 'position_id', 'rank_id']);
        });
    }
};

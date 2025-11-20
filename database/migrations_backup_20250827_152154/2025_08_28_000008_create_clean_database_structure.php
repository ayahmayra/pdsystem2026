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
        // 1. Create users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            
            // Employee fields
            $table->string('nip', 20)->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->string('position_desc')->nullable();
            $table->foreignId('rank_id')->nullable()->constrained()->nullOnDelete();
            $table->string('npwp', 25)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_no', 50)->nullable();
            $table->string('bank_account_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('signature_path')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('is_signer')->default(false);
        });

        // 2. Create units table
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20);
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('units')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Create echelons table
        Schema::create('echelons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->string('name');
            $table->timestamps();
        });

        // 4. Create ranks table
        Schema::create('ranks', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->string('name');
            $table->timestamps();
        });

        // 5. Create positions table
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable();
            $table->foreignId('echelon_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        // 6. Create provinces table
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('kemendagri_code', 10);
            $table->string('name', 100);
            $table->timestamps();
        });

        // 7. Create cities table
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('kemendagri_code', 10);
            $table->foreignId('province_id')->constrained()->onDelete('cascade');
            $table->string('name', 120);
            $table->enum('type', ['KAB', 'KOTA']);
            $table->timestamps();
        });

        // 8. Create districts table
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('kemendagri_code', 10);
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->string('name', 120);
            $table->timestamps();
        });

        // 9. Create org_places table
        Schema::create('org_places', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_org_headquarter')->default(false);
            $table->timestamps();
        });

        // 10. Create transport_modes table
        Schema::create('transport_modes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20);
            $table->string('name', 100);
            $table->timestamps();
        });

        // 11. Create travel_grades table
        Schema::create('travel_grades', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50);
            $table->string('name', 200);
            $table->timestamps();
        });

        // 12. Create user_travel_grade_maps table
        Schema::create('user_travel_grade_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('travel_grade_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 13. Create perdiem_rates table
        Schema::create('perdiem_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained()->onDelete('cascade');
            $table->foreignId('travel_grade_id')->constrained()->onDelete('cascade');
            $table->string('satuan', 10);
            $table->decimal('luar_kota', 12, 2);
            $table->decimal('dalam_kota_gt8h', 12, 2);
            $table->decimal('diklat', 12, 2);
            $table->timestamps();
        });

        // 14. Create lodging_caps table
        Schema::create('lodging_caps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained()->onDelete('cascade');
            $table->foreignId('travel_grade_id')->constrained()->onDelete('cascade');
            $table->decimal('cap_amount', 12, 2);
            $table->timestamps();
        });

        // 15. Create representation_rates table
        Schema::create('representation_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_grade_id')->constrained()->onDelete('cascade');
            $table->string('satuan', 10);
            $table->decimal('luar_kota', 12, 2);
            $table->decimal('dalam_kota_gt8h', 12, 2);
            $table->timestamps();
        });

        // 16. Create airfare_refs table
        Schema::create('airfare_refs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_city_id')->constrained('cities')->onDelete('cascade');
            $table->foreignId('destination_city_id')->constrained('cities')->onDelete('cascade');
            $table->enum('class', ['ECONOMY', 'BUSINESS']);
            $table->decimal('pp_estimate', 12, 2);
            $table->timestamps();
        });

        // 17. Create intra_province_transport_refs table
        Schema::create('intra_province_transport_refs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_place_id')->constrained('org_places')->onDelete('cascade');
            $table->foreignId('destination_city_id')->constrained('cities')->onDelete('cascade');
            $table->decimal('pp_amount', 12, 2);
            $table->timestamps();
        });

        // 18. Create intra_district_transport_refs table
        Schema::create('intra_district_transport_refs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_place_id')->constrained('org_places')->onDelete('cascade');
            $table->foreignId('destination_district_id')->constrained('districts')->onDelete('cascade');
            $table->decimal('pp_amount', 12, 2);
            $table->timestamps();
        });

        // 19. Create official_vehicle_transport_refs table
        Schema::create('official_vehicle_transport_refs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_place_id')->constrained('org_places')->onDelete('cascade');
            $table->foreignId('destination_district_id')->constrained('districts')->onDelete('cascade');
            $table->decimal('pp_amount', 12, 2);
            $table->string('context');
            $table->timestamps();
        });

        // 20. Create atcost_components table
        Schema::create('atcost_components', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->timestamps();
        });

        // 21. Create doc_number_formats table
        Schema::create('doc_number_formats', function (Blueprint $table) {
            $table->id();
            $table->string('doc_type');
            $table->foreignId('unit_scope_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('format_string');
            $table->string('doc_code');
            $table->enum('reset_policy', ['NEVER', 'YEARLY', 'MONTHLY']);
            $table->unsignedTinyInteger('padding');
            $table->boolean('is_active')->default(true);
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // 22. Create number_sequences table
        Schema::create('number_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('doc_type');
            $table->foreignId('unit_scope_id')->nullable()->constrained('units')->nullOnDelete();
            $table->unsignedSmallInteger('year_scope')->nullable();
            $table->unsignedTinyInteger('month_scope')->nullable();
            $table->unsignedBigInteger('current_value');
            $table->timestamp('last_generated_at')->nullable();
            $table->timestamps();
        });

        // 23. Create document_numbers table
        Schema::create('document_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('doc_type');
            $table->unsignedBigInteger('doc_id')->nullable();
            $table->string('number');
            $table->foreignId('generated_by_user_id')->constrained('users');
            $table->boolean('is_manual');
            $table->string('old_number')->nullable();
            $table->foreignId('format_id')->nullable()->constrained('doc_number_formats')->nullOnDelete();
            $table->foreignId('sequence_id')->nullable()->constrained('number_sequences')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamp('created_at');
        });

        // 24. Create org_settings table
        Schema::create('org_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->foreignId('head_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('head_title');
            $table->string('signature_path')->nullable();
            $table->string('stamp_path')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('singleton')->default(false);
            $table->timestamps();
        });

        // 25. Create nota_dinas table
        Schema::create('nota_dinas', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->unique();
            $table->boolean('number_is_manual')->default(false);
            $table->text('number_manual_reason')->nullable();
            $table->foreignId('number_format_id')->nullable()->constrained('doc_number_formats')->nullOnDelete();
            $table->foreignId('number_sequence_id')->nullable()->constrained('number_sequences')->nullOnDelete();
            $table->foreignId('number_scope_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('to_user_id')->constrained('users');
            $table->foreignId('from_user_id')->constrained('users');
            $table->text('tembusan')->nullable();
            $table->date('nd_date');
            $table->string('sifat')->nullable();
            $table->integer('lampiran_count');
            $table->string('hal');
            $table->text('dasar');
            $table->text('maksud');
            $table->foreignId('destination_city_id')->constrained('cities');
            $table->foreignId('origin_place_id')->nullable()->constrained('org_places')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('trip_type', ['LUAR_DAERAH', 'DALAM_DAERAH_GT8H', 'DALAM_DAERAH_LE8H', 'DIKLAT']);
            $table->foreignId('requesting_unit_id')->constrained('units');
            $table->enum('status', ['DRAFT', 'SUBMITTED', 'APPROVED', 'REJECTED']);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 26. Create nota_dinas_participants table
        Schema::create('nota_dinas_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_dinas_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role_in_trip')->nullable();
            $table->timestamps();
        });

        // 27. Create spt table
        Schema::create('spt', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->unique();
            $table->boolean('number_is_manual')->default(false);
            $table->text('number_manual_reason')->nullable();
            $table->foreignId('number_format_id')->nullable()->constrained('doc_number_formats')->nullOnDelete();
            $table->foreignId('number_sequence_id')->nullable()->constrained('number_sequences')->nullOnDelete();
            $table->foreignId('number_scope_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->date('spt_date');
            $table->foreignId('nota_dinas_id')->constrained('nota_dinas');
            $table->foreignId('signed_by_user_id')->constrained('users');
            $table->text('assignment_title');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 28. Create spt_members table
        Schema::create('spt_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spt_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 29. Create sppd table
        Schema::create('sppd', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->unique();
            $table->boolean('number_is_manual')->default(false);
            $table->text('number_manual_reason')->nullable();
            $table->foreignId('number_format_id')->nullable()->constrained('doc_number_formats')->nullOnDelete();
            $table->foreignId('number_sequence_id')->nullable()->constrained('number_sequences')->nullOnDelete();
            $table->foreignId('number_scope_unit_id')->constrained('units');
            $table->date('sppd_date');
            $table->foreignId('spt_id')->constrained('spt');
            $table->foreignId('signed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('assignment_title')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->string('funding_source')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 30. Create sppd_transport_modes table
        Schema::create('sppd_transport_modes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppd_id')->constrained()->onDelete('cascade');
            $table->foreignId('transport_mode_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['sppd_id', 'transport_mode_id']);
        });

        // 31. Create sppd_itineraries table
        Schema::create('sppd_itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppd_id')->constrained()->onDelete('cascade');
            $table->integer('leg_no');
            $table->date('date')->nullable();
            $table->string('from_place');
            $table->string('to_place');
            $table->string('mode_detail');
            $table->string('ticket_no')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });

        // 32. Create sppd_divisum_signoffs table
        Schema::create('sppd_divisum_signoffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppd_id')->constrained()->onDelete('cascade');
            $table->string('signed_place');
            $table->date('signed_date');
            $table->string('signed_by_name');
            $table->string('signed_by_position');
            $table->string('note')->nullable();
            $table->timestamps();
        });

        // 33. Create receipts table
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->unique();
            $table->boolean('number_is_manual')->default(false);
            $table->text('number_manual_reason')->nullable();
            $table->foreignId('number_format_id')->nullable()->constrained('doc_number_formats')->nullOnDelete();
            $table->foreignId('number_sequence_id')->nullable()->constrained('number_sequences')->nullOnDelete();
            $table->foreignId('number_scope_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('sppd_id')->constrained('sppd');
            $table->foreignId('travel_grade_id')->constrained('travel_grades');
            $table->string('receipt_no')->nullable();
            $table->date('receipt_date')->nullable();
            $table->foreignId('payee_user_id')->constrained('users');
            $table->decimal('total_amount', 16, 2);
            $table->text('notes')->nullable();
            $table->enum('status', ['DRAFT', 'FINAL']);
            $table->timestamps();
            $table->softDeletes();
        });

        // 34. Create receipt_lines table
        Schema::create('receipt_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->constrained()->onDelete('cascade');
            $table->enum('component', [
                'PERDIEM', 'REPRESENTASI', 'LODGING', 'AIRFARE', 'INTRA_PROV', 
                'INTRA_DISTRICT', 'OFFICIAL_VEHICLE', 'TAXI', 'RORO', 'TOLL', 
                'PARKIR_INAP', 'RAPID_TEST', 'LAINNYA'
            ]);
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

        // 35. Create trip_reports table
        Schema::create('trip_reports', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->unique();
            $table->boolean('number_is_manual')->default(false);
            $table->text('number_manual_reason')->nullable();
            $table->foreignId('number_format_id')->nullable()->constrained('doc_number_formats')->nullOnDelete();
            $table->foreignId('number_sequence_id')->nullable()->constrained('number_sequences')->nullOnDelete();
            $table->foreignId('number_scope_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('spt_id')->constrained('spt');
            $table->string('report_no')->nullable();
            $table->date('report_date')->nullable();
            $table->string('place_from');
            $table->string('place_to');
            $table->date('depart_date');
            $table->date('return_date');
            $table->longText('activities');
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // 36. Create trip_report_signers table
        Schema::create('trip_report_signers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_report_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('nip')->nullable();
            $table->string('position')->nullable();
            $table->timestamps();
        });

        // 37. Create supporting_documents table
        Schema::create('supporting_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_dinas_id')->constrained()->onDelete('cascade');
            $table->string('document_type');
            $table->string('file_path');
            $table->string('file_name');
            $table->bigInteger('file_size');
            $table->string('mime_type');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 38. Create district_perdiem_rates table
        Schema::create('district_perdiem_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->onDelete('cascade');
            $table->foreignId('travel_grade_id')->constrained()->onDelete('cascade');
            $table->decimal('perdiem_rate', 12, 2);
            $table->timestamps();
        });

        // 39. Create travel_routes table
        Schema::create('travel_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_place_id')->constrained('org_places')->onDelete('cascade');
            $table->foreignId('destination_place_id')->constrained('org_places')->onDelete('cascade');
            $table->foreignId('mode_id')->constrained('transport_modes')->onDelete('cascade');
            $table->boolean('is_roundtrip')->default(false);
            $table->enum('class', ['ECONOMY', 'BUSINESS'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order
        Schema::dropIfExists('travel_routes');
        Schema::dropIfExists('district_perdiem_rates');
        Schema::dropIfExists('supporting_documents');
        Schema::dropIfExists('trip_report_signers');
        Schema::dropIfExists('trip_reports');
        Schema::dropIfExists('receipt_lines');
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('sppd_divisum_signoffs');
        Schema::dropIfExists('sppd_itineraries');
        Schema::dropIfExists('sppd_transport_modes');
        Schema::dropIfExists('sppd');
        Schema::dropIfExists('spt_members');
        Schema::dropIfExists('spt');
        Schema::dropIfExists('nota_dinas_participants');
        Schema::dropIfExists('nota_dinas');
        Schema::dropIfExists('org_settings');
        Schema::dropIfExists('document_numbers');
        Schema::dropIfExists('number_sequences');
        Schema::dropIfExists('doc_number_formats');
        Schema::dropIfExists('atcost_components');
        Schema::dropIfExists('official_vehicle_transport_refs');
        Schema::dropIfExists('intra_district_transport_refs');
        Schema::dropIfExists('intra_province_transport_refs');
        Schema::dropIfExists('airfare_refs');
        Schema::dropIfExists('representation_rates');
        Schema::dropIfExists('lodging_caps');
        Schema::dropIfExists('perdiem_rates');
        Schema::dropIfExists('user_travel_grade_maps');
        Schema::dropIfExists('travel_grades');
        Schema::dropIfExists('transport_modes');
        Schema::dropIfExists('org_places');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('ranks');
        Schema::dropIfExists('echelons');
        Schema::dropIfExists('units');
        Schema::dropIfExists('users');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Check if a foreign key constraint exists on the table.
     */
    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $result = DB::select(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = ?",
            [DB::getDatabaseName(), $table, $constraintName]
        );
        return !empty($result);
    }

    /**
     * Run a migration step, catching integrity constraint violations (orphaned data).
     */
    private function safeAddForeignKeys(string $table, callable $callback): void
    {
        try {
            $callback();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), '1452')) {
                return;
            }
            throw $e;
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add foreign key constraints for users table
        if (Schema::hasTable('users')) {
        Schema::table('users', function (Blueprint $table) {
            if (!$this->foreignKeyExists('users', 'users_unit_id_foreign')) {
                $table->foreign('unit_id')->references('id')->on('units')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('users', 'users_position_id_foreign')) {
                $table->foreign('position_id')->references('id')->on('positions')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('users', 'users_rank_id_foreign')) {
                $table->foreign('rank_id')->references('id')->on('ranks')->nullOnDelete();
            }
        });
        }

        // Add foreign key constraints for units table
        if (Schema::hasTable('units')) {
        Schema::table('units', function (Blueprint $table) {
            if (!$this->foreignKeyExists('units', 'units_parent_id_foreign')) {
                $table->foreign('parent_id')->references('id')->on('units')->nullOnDelete();
            }
        });
        }

        // Add foreign key constraints for positions table
        if (Schema::hasTable('positions')) {
        Schema::table('positions', function (Blueprint $table) {
            if (!$this->foreignKeyExists('positions', 'positions_echelon_id_foreign')) {
                $table->foreign('echelon_id')->references('id')->on('echelons')->nullOnDelete();
            }
        });
        }

        // Add foreign key constraints for cities table
        if (Schema::hasTable('cities')) {
        Schema::table('cities', function (Blueprint $table) {
            if (!$this->foreignKeyExists('cities', 'cities_province_id_foreign')) {
                $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for districts table
        if (Schema::hasTable('districts')) {
        Schema::table('districts', function (Blueprint $table) {
            if (!$this->foreignKeyExists('districts', 'districts_city_id_foreign')) {
                $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for org_places table
        if (Schema::hasTable('org_places')) {
        Schema::table('org_places', function (Blueprint $table) {
            if (!$this->foreignKeyExists('org_places', 'org_places_city_id_foreign')) {
                $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('org_places', 'org_places_district_id_foreign')) {
                $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
            }
        });
        }

        // Add foreign key constraints for user_travel_grade_maps table
        if (Schema::hasTable('user_travel_grade_maps')) {
        Schema::table('user_travel_grade_maps', function (Blueprint $table) {
            if (!$this->foreignKeyExists('user_travel_grade_maps', 'user_travel_grade_maps_user_id_foreign')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('user_travel_grade_maps', 'user_travel_grade_maps_travel_grade_id_foreign')) {
                $table->foreign('travel_grade_id')->references('id')->on('travel_grades')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for perdiem_rates table
        if (Schema::hasTable('perdiem_rates')) {
        Schema::table('perdiem_rates', function (Blueprint $table) {
            if (!$this->foreignKeyExists('perdiem_rates', 'perdiem_rates_province_id_foreign')) {
                $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('perdiem_rates', 'perdiem_rates_travel_grade_id_foreign')) {
                $table->foreign('travel_grade_id')->references('id')->on('travel_grades')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for lodging_caps table
        if (Schema::hasTable('lodging_caps')) {
        Schema::table('lodging_caps', function (Blueprint $table) {
            if (!$this->foreignKeyExists('lodging_caps', 'lodging_caps_province_id_foreign')) {
                $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('lodging_caps', 'lodging_caps_travel_grade_id_foreign')) {
                $table->foreign('travel_grade_id')->references('id')->on('travel_grades')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for representation_rates table
        if (Schema::hasTable('representation_rates')) {
        Schema::table('representation_rates', function (Blueprint $table) {
            if (!$this->foreignKeyExists('representation_rates', 'representation_rates_travel_grade_id_foreign')) {
                $table->foreign('travel_grade_id')->references('id')->on('travel_grades')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for airfare_refs table
        if (Schema::hasTable('airfare_refs')) {
        Schema::table('airfare_refs', function (Blueprint $table) {
            if (!$this->foreignKeyExists('airfare_refs', 'airfare_refs_origin_city_id_foreign')) {
                $table->foreign('origin_city_id')->references('id')->on('cities')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('airfare_refs', 'airfare_refs_destination_city_id_foreign')) {
                $table->foreign('destination_city_id')->references('id')->on('cities')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for intra_province_transport_refs table
        if (Schema::hasTable('intra_province_transport_refs')) {
        Schema::table('intra_province_transport_refs', function (Blueprint $table) {
            if (!$this->foreignKeyExists('intra_province_transport_refs', 'intra_province_transport_refs_origin_place_id_foreign')) {
                $table->foreign('origin_place_id')->references('id')->on('org_places')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('intra_province_transport_refs', 'intra_province_transport_refs_destination_city_id_foreign')) {
                $table->foreign('destination_city_id')->references('id')->on('cities')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for intra_district_transport_refs table
        if (Schema::hasTable('intra_district_transport_refs')) {
        Schema::table('intra_district_transport_refs', function (Blueprint $table) {
            if (!$this->foreignKeyExists('intra_district_transport_refs', 'intra_district_transport_refs_origin_place_id_foreign')) {
                $table->foreign('origin_place_id')->references('id')->on('org_places')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('intra_district_transport_refs', 'intra_district_transport_refs_destination_district_id_foreign')) {
                $table->foreign('destination_district_id')->references('id')->on('districts')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for official_vehicle_transport_refs table
        if (Schema::hasTable('official_vehicle_transport_refs')) {
        Schema::table('official_vehicle_transport_refs', function (Blueprint $table) {
            if (!$this->foreignKeyExists('official_vehicle_transport_refs', 'official_vehicle_transport_refs_origin_place_id_foreign')) {
                $table->foreign('origin_place_id')->references('id')->on('org_places')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('official_vehicle_transport_refs', 'official_vehicle_transport_refs_destination_district_id_foreign')) {
                $table->foreign('destination_district_id')->references('id')->on('districts')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for doc_number_formats table
        if (Schema::hasTable('doc_number_formats')) {
        Schema::table('doc_number_formats', function (Blueprint $table) {
            if (!$this->foreignKeyExists('doc_number_formats', 'doc_number_formats_unit_scope_id_foreign')) {
                $table->foreign('unit_scope_id')->references('id')->on('units')->nullOnDelete();
            }
        });
        }

        // Add foreign key constraints for number_sequences table
        if (Schema::hasTable('number_sequences')) {
        Schema::table('number_sequences', function (Blueprint $table) {
            if (!$this->foreignKeyExists('number_sequences', 'number_sequences_unit_scope_id_foreign')) {
                $table->foreign('unit_scope_id')->references('id')->on('units')->nullOnDelete();
            }
        });
        }

        // Add foreign key constraints for document_numbers table
        if (Schema::hasTable('document_numbers')) {
        Schema::table('document_numbers', function (Blueprint $table) {
            if (!$this->foreignKeyExists('document_numbers', 'document_numbers_generated_by_user_id_foreign')) {
                $table->foreign('generated_by_user_id')->references('id')->on('users');
            }
            if (!$this->foreignKeyExists('document_numbers', 'document_numbers_format_id_foreign')) {
                $table->foreign('format_id')->references('id')->on('doc_number_formats')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('document_numbers', 'document_numbers_sequence_id_foreign')) {
                $table->foreign('sequence_id')->references('id')->on('number_sequences')->nullOnDelete();
            }
        });
        }

        // Add foreign key constraints for org_settings table
        if (Schema::hasTable('org_settings')) {
        Schema::table('org_settings', function (Blueprint $table) {
            if (!$this->foreignKeyExists('org_settings', 'org_settings_head_user_id_foreign')) {
                $table->foreign('head_user_id')->references('id')->on('users')->nullOnDelete();
            }
        });
        }

        // Add foreign key constraints for nota_dinas table
        if (Schema::hasTable('nota_dinas')) {
        Schema::table('nota_dinas', function (Blueprint $table) {
            if (!$this->foreignKeyExists('nota_dinas', 'nota_dinas_number_format_id_foreign')) {
                $table->foreign('number_format_id')->references('id')->on('doc_number_formats')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('nota_dinas', 'nota_dinas_number_sequence_id_foreign')) {
                $table->foreign('number_sequence_id')->references('id')->on('number_sequences')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('nota_dinas', 'nota_dinas_number_scope_unit_id_foreign')) {
                $table->foreign('number_scope_unit_id')->references('id')->on('units')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('nota_dinas', 'nota_dinas_to_user_id_foreign')) {
                $table->foreign('to_user_id')->references('id')->on('users');
            }
            if (!$this->foreignKeyExists('nota_dinas', 'nota_dinas_from_user_id_foreign')) {
                $table->foreign('from_user_id')->references('id')->on('users');
            }
            if (!$this->foreignKeyExists('nota_dinas', 'nota_dinas_destination_city_id_foreign')) {
                $table->foreign('destination_city_id')->references('id')->on('cities');
            }
            if (!$this->foreignKeyExists('nota_dinas', 'nota_dinas_origin_place_id_foreign')) {
                $table->foreign('origin_place_id')->references('id')->on('org_places')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('nota_dinas', 'nota_dinas_requesting_unit_id_foreign')) {
                $table->foreign('requesting_unit_id')->references('id')->on('units');
            }
            if (!$this->foreignKeyExists('nota_dinas', 'nota_dinas_created_by_foreign')) {
                $table->foreign('created_by')->references('id')->on('users');
            }
            if (!$this->foreignKeyExists('nota_dinas', 'nota_dinas_approved_by_foreign')) {
                $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            }
        });
        }

        // Add foreign key constraints for nota_dinas_participants table
        if (Schema::hasTable('nota_dinas_participants')) {
        Schema::table('nota_dinas_participants', function (Blueprint $table) {
            if (!$this->foreignKeyExists('nota_dinas_participants', 'nota_dinas_participants_nota_dinas_id_foreign')) {
                $table->foreign('nota_dinas_id')->references('id')->on('nota_dinas')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('nota_dinas_participants', 'nota_dinas_participants_user_id_foreign')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for spt table
        if (Schema::hasTable('spt')) {
        Schema::table('spt', function (Blueprint $table) {
            if (!$this->foreignKeyExists('spt', 'spt_number_format_id_foreign')) {
                $table->foreign('number_format_id')->references('id')->on('doc_number_formats')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('spt', 'spt_number_sequence_id_foreign')) {
                $table->foreign('number_sequence_id')->references('id')->on('number_sequences')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('spt', 'spt_number_scope_unit_id_foreign')) {
                $table->foreign('number_scope_unit_id')->references('id')->on('units')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('spt', 'spt_nota_dinas_id_foreign')) {
                $table->foreign('nota_dinas_id')->references('id')->on('nota_dinas');
            }
            if (!$this->foreignKeyExists('spt', 'spt_signed_by_user_id_foreign')) {
                $table->foreign('signed_by_user_id')->references('id')->on('users');
            }
        });
        }

        // Add foreign key constraints for spt_members table
        if (Schema::hasTable('spt_members')) {
        Schema::table('spt_members', function (Blueprint $table) {
            if (!$this->foreignKeyExists('spt_members', 'spt_members_spt_id_foreign')) {
                $table->foreign('spt_id')->references('id')->on('spt')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('spt_members', 'spt_members_user_id_foreign')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for sppd table
        if (Schema::hasTable('sppd')) {
        Schema::table('sppd', function (Blueprint $table) {
            if (!$this->foreignKeyExists('sppd', 'sppd_number_format_id_foreign')) {
                $table->foreign('number_format_id')->references('id')->on('doc_number_formats')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('sppd', 'sppd_number_sequence_id_foreign')) {
                $table->foreign('number_sequence_id')->references('id')->on('number_sequences')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('sppd', 'sppd_number_scope_unit_id_foreign')) {
                $table->foreign('number_scope_unit_id')->references('id')->on('units');
            }
            if (!$this->foreignKeyExists('sppd', 'sppd_spt_id_foreign')) {
                $table->foreign('spt_id')->references('id')->on('spt');
            }
            if (!$this->foreignKeyExists('sppd', 'sppd_signed_by_user_id_foreign')) {
                $table->foreign('signed_by_user_id')->references('id')->on('users')->nullOnDelete();
            }
        });
        }

        // Add foreign key constraints for sppd_transport_modes table
        if (Schema::hasTable('sppd_transport_modes')) {
        Schema::table('sppd_transport_modes', function (Blueprint $table) {
            if (!$this->foreignKeyExists('sppd_transport_modes', 'sppd_transport_modes_sppd_id_foreign')) {
                $table->foreign('sppd_id')->references('id')->on('sppd')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('sppd_transport_modes', 'sppd_transport_modes_transport_mode_id_foreign')) {
                $table->foreign('transport_mode_id')->references('id')->on('transport_modes')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for sppd_itineraries table
        if (Schema::hasTable('sppd_itineraries')) {
        Schema::table('sppd_itineraries', function (Blueprint $table) {
            if (!$this->foreignKeyExists('sppd_itineraries', 'sppd_itineraries_sppd_id_foreign')) {
                $table->foreign('sppd_id')->references('id')->on('sppd')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for sppd_divisum_signoffs table
        if (Schema::hasTable('sppd_divisum_signoffs')) {
        Schema::table('sppd_divisum_signoffs', function (Blueprint $table) {
            if (!$this->foreignKeyExists('sppd_divisum_signoffs', 'sppd_divisum_signoffs_sppd_id_foreign')) {
                $table->foreign('sppd_id')->references('id')->on('sppd')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for receipts table
        if (Schema::hasTable('receipts')) {
        Schema::table('receipts', function (Blueprint $table) {
            if (!$this->foreignKeyExists('receipts', 'receipts_number_format_id_foreign')) {
                $table->foreign('number_format_id')->references('id')->on('doc_number_formats')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('receipts', 'receipts_number_sequence_id_foreign')) {
                $table->foreign('number_sequence_id')->references('id')->on('number_sequences')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('receipts', 'receipts_number_scope_unit_id_foreign')) {
                $table->foreign('number_scope_unit_id')->references('id')->on('units')->nullOnDelete();
            }
            if (!$this->foreignKeyExists('receipts', 'receipts_sppd_id_foreign')) {
                $table->foreign('sppd_id')->references('id')->on('sppd');
            }
            if (!$this->foreignKeyExists('receipts', 'receipts_travel_grade_id_foreign')) {
                $table->foreign('travel_grade_id')->references('id')->on('travel_grades');
            }
            if (!$this->foreignKeyExists('receipts', 'receipts_payee_user_id_foreign')) {
                $table->foreign('payee_user_id')->references('id')->on('users');
            }
        });
        }

        // Add foreign key constraints for receipt_lines table
        if (Schema::hasTable('receipt_lines')) {
        Schema::table('receipt_lines', function (Blueprint $table) {
            if (!$this->foreignKeyExists('receipt_lines', 'receipt_lines_receipt_id_foreign')) {
                $table->foreign('receipt_id')->references('id')->on('receipts')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for trip_reports table
        if (Schema::hasTable('trip_reports')) {
            $this->safeAddForeignKeys('trip_reports', function () {
                Schema::table('trip_reports', function (Blueprint $table) {
                    if (!$this->foreignKeyExists('trip_reports', 'trip_reports_number_format_id_foreign')) {
                        $table->foreign('number_format_id')->references('id')->on('doc_number_formats')->nullOnDelete();
                    }
                    if (!$this->foreignKeyExists('trip_reports', 'trip_reports_number_sequence_id_foreign')) {
                        $table->foreign('number_sequence_id')->references('id')->on('number_sequences')->nullOnDelete();
                    }
                    if (!$this->foreignKeyExists('trip_reports', 'trip_reports_number_scope_unit_id_foreign')) {
                        $table->foreign('number_scope_unit_id')->references('id')->on('units')->nullOnDelete();
                    }
                    if (!$this->foreignKeyExists('trip_reports', 'trip_reports_spt_id_foreign')) {
                        $table->foreign('spt_id')->references('id')->on('spt');
                    }
                    if (!$this->foreignKeyExists('trip_reports', 'trip_reports_created_by_user_id_foreign')) {
                        $table->foreign('created_by_user_id')->references('id')->on('users');
                    }
                });
            });
        }

        // Add foreign key constraints for trip_report_signers table
        if (Schema::hasTable('trip_report_signers')) {
        Schema::table('trip_report_signers', function (Blueprint $table) {
            if (!$this->foreignKeyExists('trip_report_signers', 'trip_report_signers_trip_report_id_foreign')) {
                $table->foreign('trip_report_id')->references('id')->on('trip_reports')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for supporting_documents table
        if (Schema::hasTable('supporting_documents')) {
        Schema::table('supporting_documents', function (Blueprint $table) {
            if (!$this->foreignKeyExists('supporting_documents', 'supporting_documents_nota_dinas_id_foreign')) {
                $table->foreign('nota_dinas_id')->references('id')->on('nota_dinas')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for district_perdiem_rates table
        if (Schema::hasTable('district_perdiem_rates')) {
        Schema::table('district_perdiem_rates', function (Blueprint $table) {
            if (!$this->foreignKeyExists('district_perdiem_rates', 'district_perdiem_rates_district_id_foreign')) {
                $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('district_perdiem_rates', 'district_perdiem_rates_travel_grade_id_foreign')) {
                $table->foreign('travel_grade_id')->references('id')->on('travel_grades')->onDelete('cascade');
            }
        });
        }

        // Add foreign key constraints for travel_routes table
        if (Schema::hasTable('travel_routes')) {
        Schema::table('travel_routes', function (Blueprint $table) {
            if (!$this->foreignKeyExists('travel_routes', 'travel_routes_origin_place_id_foreign')) {
                $table->foreign('origin_place_id')->references('id')->on('org_places')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('travel_routes', 'travel_routes_destination_place_id_foreign')) {
                $table->foreign('destination_place_id')->references('id')->on('org_places')->onDelete('cascade');
            }
            if (!$this->foreignKeyExists('travel_routes', 'travel_routes_mode_id_foreign')) {
                $table->foreign('mode_id')->references('id')->on('transport_modes')->onDelete('cascade');
            }
        });
        }
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus semua migration lama dari tabel migrations
        DB::table('migrations')->truncate();
        
        // Masukkan migration bersih sebagai satu-satunya migration
        DB::table('migrations')->insert([
            'migration' => '2025_08_28_000008_create_clean_database_structure',
            'batch' => 1,
        ]);
        
        echo "Migration cleanup completed. Only clean migration remains.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan migration lama (jika diperlukan)
        $oldMigrations = [
            '0001_01_01_000000_create_users_table',
            '0001_01_01_000001_create_cache_table',
            '0001_01_01_000002_create_jobs_table',
            '2025_08_09_120232_create_units_table',
            '2025_08_09_120239_create_positions_table',
            '2025_08_09_120245_create_ranks_table',
            '2025_08_09_120251_create_echelons_table',
            '2025_08_09_120310_add_employee_fields_to_users_table',
            '2025_08_09_120322_create_org_settings_table',
            '2025_08_09_120334_create_doc_number_formats_table',
            '2025_08_09_120340_create_number_sequences_table',
            '2025_08_09_141403_add_echelon_id_to_positions_table',
            '2025_08_09_141422_remove_echelon_id_from_users_table',
            '2025_08_10_054016_create_provinces_table',
            '2025_08_10_054029_create_cities_table',
            '2025_08_10_054035_create_districts_table',
            '2025_08_10_054042_create_org_places_table',
            '2025_08_10_085324_create_transport_modes_table',
            '2025_08_10_085341_create_travel_routes_table',
            '2025_08_10_094758_create_travel_grades_table',
            '2025_08_10_094838_create_user_travel_grade_maps_table',
            '2025_08_10_112308_create_perdiem_rates_table',
            '2025_08_10_125858_create_lodging_caps_table',
            '2025_08_10_132247_create_representation_rates_table',
            '2025_08_11_113655_create_airfare_refs_table',
            '2025_08_13_011112_create_intra_province_transport_refs_table',
            '2025_08_13_050701_create_intra_district_transport_refs_table',
            '2025_08_13_053721_create_official_vehicle_transport_refs_table',
            '2025_08_13_062245_create_atcost_components_table',
            '2025_08_16_114315_create_nota_dinas_table',
            '2025_08_16_114321_create_nota_dinas_participants_table',
            '2025_08_16_114327_create_spt_table',
            '2025_08_16_114334_create_spt_members_table',
            '2025_08_16_114340_create_sppd_table',
            '2025_08_16_114346_create_sppd_itineraries_table',
            '2025_08_16_114353_create_sppd_divisum_signoffs_table',
            '2025_08_16_114401_create_receipts_table',
            '2025_08_16_114408_create_receipt_lines_table',
            '2025_08_16_114416_create_trip_reports_table',
            '2025_08_16_114424_create_trip_report_signers_table',
            '2025_08_16_123754_create_doc_number_formats_table',
            '2025_08_16_123802_create_number_sequences_table',
            '2025_08_16_123812_create_document_numbers_table',
            '2025_08_18_020000_alter_document_numbers_doc_id_nullable',
            '2025_08_18_030000_drop_spt_request_date_from_nota_dinas_table',
            '2025_08_18_040000_drop_signer_user_id_from_nota_dinas_table',
            '2025_08_21_000001_add_position_desc_to_users_table',
            '2025_08_22_000001_slim_down_spt_table',
            '2025_08_23_000002_drop_spt_members_table',
            '2025_08_23_000003_drop_status_from_spt_table',
            '2025_08_23_045206_remove_status_from_sppd_table',
            '2025_08_23_052142_remove_start_date_and_end_date_from_sppd_table',
            '2025_08_23_052656_remove_days_count_from_nota_dinas_and_sppd_tables',
            '2025_08_23_053626_create_sppd_transport_modes_table',
            '2025_08_23_053643_remove_transport_mode_id_from_sppd_table',
            '2025_08_24_130112_update_receipt_lines_table_simplified',
            '2025_08_24_130244_remove_total_amount_from_receipts_table',
            '2025_08_24_132140_fix_sppd_doc_no_unique_constraint_per_unit',
            '2025_08_24_132613_fix_receipts_doc_no_unique_constraint_per_unit',
            '2025_08_24_132847_fix_document_numbers_unique_constraint_per_scope',
            '2025_08_24_145059_create_district_perdiem_rates_table',
            '2025_08_25_013206_create_supporting_documents_table',
            '2025_08_25_021323_add_origin_place_id_to_nota_dinas_table',
            '2025_08_25_021637_drop_status_from_trip_reports_table',
            '2025_08_25_022241_drop_origin_place_id_from_sppd_table',
            '2025_08_25_022725_drop_destination_city_id_from_sppd_table',
            '2025_08_25_033453_allow_null_doc_no_in_trip_reports_table',
            '2025_08_25_042505_change_supporting_documents_relation_to_nota_dinas',
            '2025_08_25_120238_change_assignment_title_to_text_in_spt_table',
            '2025_08_25_125048_add_signed_by_user_id_and_assignment_title_to_sppd_table',
            '2025_08_27_065733_move_trip_type_from_sppd_to_nota_dinas',
            '2025_08_28_000001_fix_nota_dinas_table_structure',
            '2025_08_28_000002_fix_sppd_table_structure',
            '2025_08_28_000003_fix_spt_table_structure',
            '2025_08_28_000004_fix_trip_reports_table_structure',
            '2025_08_28_000005_create_sppd_transport_modes_table',
            '2025_08_28_000006_mark_old_migrations_as_completed',
            '2025_08_28_000007_fix_spt_table_remove_duplicate_fields',
        ];
        
        foreach ($oldMigrations as $migration) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => 1,
            ]);
        }
    }
};

<?php

namespace Database\Seeders;

use App\Models\DocNumberFormat;
use Illuminate\Database\Seeder;

class DocNumberFormatSeeder extends Seeder
{
    public function run(): void
    {
        // Format untuk Nota Dinas
        DocNumberFormat::updateOrCreate([
            'doc_type' => 'ND',
            'unit_scope_id' => null,
        ], [
            'format_string' => '{seq}/ND/{unit_code}/{roman_month}/{year}',
            'doc_code' => 'ND',
            'reset_policy' => 'YEARLY',
            'padding' => 3,
            'is_active' => 1,
            'notes' => 'Format ND global',
        ]);

        // Format untuk SPT
        DocNumberFormat::updateOrCreate([
            'doc_type' => 'SPT',
            'unit_scope_id' => null,
        ], [
            'format_string' => '{seq}/SPT/{unit_code}/{roman_month}/{year}',
            'doc_code' => 'SPT',
            'reset_policy' => 'YEARLY',
            'padding' => 3,
            'is_active' => 1,
            'notes' => 'Format SPT global',
        ]);

        // Format untuk SPPD
        DocNumberFormat::updateOrCreate([
            'doc_type' => 'SPPD',
            'unit_scope_id' => null,
        ], [
            'format_string' => '{seq}/SPPD/{unit_code}/{roman_month}/{year}',
            'doc_code' => 'SPPD',
            'reset_policy' => 'YEARLY',
            'padding' => 3,
            'is_active' => 1,
            'notes' => 'Format SPPD global',
        ]);

        // Format untuk Trip Report
        DocNumberFormat::updateOrCreate([
            'doc_type' => 'TR',
            'unit_scope_id' => null,
        ], [
            'format_string' => '{seq}/TR/{unit_code}/{roman_month}/{year}',
            'doc_code' => 'TR',
            'reset_policy' => 'YEARLY',
            'padding' => 3,
            'is_active' => 1,
            'notes' => 'Format Trip Report global',
        ]);

        // Format untuk Receipt/Kwitansi
        DocNumberFormat::updateOrCreate([
            'doc_type' => 'KW',
            'unit_scope_id' => null,
        ], [
            'format_string' => '{seq}/KW/{unit_code}/{roman_month}/{year}',
            'doc_code' => 'KW',
            'reset_policy' => 'YEARLY',
            'padding' => 3,
            'is_active' => 1,
            'notes' => 'Format Kwitansi global',
        ]);
    }
}

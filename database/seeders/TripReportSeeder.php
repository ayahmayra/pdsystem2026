<?php

namespace Database\Seeders;

use App\Models\TripReport;
use App\Models\Spt;
use App\Services\DocumentNumberService;
use Illuminate\Database\Seeder;

class TripReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spts = Spt::with(['notaDinas'])->get();

        if ($spts->isEmpty()) {
            return;
        }

        foreach ($spts as $spt) {
            // Create trip report for each SPT
            $docNumberResult = DocumentNumberService::generate('TR', $spt->number_scope_unit_id, now(), [
                'spt_id' => $spt->id,
            ], $spt->signed_by_user_id);

            TripReport::create([
                'doc_no' => $docNumberResult['number'],
                'number_is_manual' => false,
                'number_format_id' => $docNumberResult['format']->id ?? null,
                'number_sequence_id' => $docNumberResult['sequence']->id ?? null,
                'number_scope_unit_id' => $spt->number_scope_unit_id,
                'spt_id' => $spt->id,
                'report_no' => 'LPD-' . $spt->doc_no,
                'report_date' => now(),
                'place_from' => $spt->notaDinas->originPlace->name ?? 'Bengkalis',
                'place_to' => $spt->notaDinas->destinationCity->name ?? 'Jakarta',
                'depart_date' => $spt->start_date,
                'return_date' => $spt->end_date,
                'activities' => 'Melakukan tugas perjalanan dinas sesuai dengan surat tugas yang diberikan. Kegiatan meliputi koordinasi, rapat, dan kunjungan kerja.',
                'created_by_user_id' => $spt->signed_by_user_id,
                'status' => 'SUBMITTED',
            ]);
        }
    }
}

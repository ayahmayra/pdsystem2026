<?php

namespace Database\Seeders;

use App\Models\Sppd;
use App\Models\Spt;
use App\Models\TransportMode;
use App\Services\DocumentNumberService;
use Illuminate\Database\Seeder;

class SppdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spts = Spt::with(['members.user', 'notaDinas'])->get();
        $transportModes = TransportMode::all();

        if ($spts->isEmpty() || $transportModes->isEmpty()) {
            return;
        }

        foreach ($spts as $spt) {
            foreach ($spt->members as $member) {
                // Create SPPD for each SPT member
                $docNumberResult = DocumentNumberService::generate('SPPD', $spt->number_scope_unit_id, now(), [
                    'spt_id' => $spt->id,
                    'user_id' => $member->user_id,
                ], $member->user_id);

                $sppd = Sppd::create([
                    'doc_no' => $docNumberResult['number'],
                    'number_is_manual' => false,
                    'number_format_id' => $docNumberResult['format']->id ?? null,
                    'number_sequence_id' => $docNumberResult['sequence']->id ?? null,
                    'number_scope_unit_id' => $spt->number_scope_unit_id,
                    'sppd_date' => now(),
                    'spt_id' => $spt->id,
                    'user_id' => $member->user_id,
                    'signed_by_user_id' => $spt->signed_by_user_id,
                    'assignment_title' => $spt->assignment_title,
                    'origin_place_id' => $spt->origin_place_id,
                    'destination_city_id' => $spt->destination_city_id,
                    'funding_source' => $spt->funding_source,
                    'status' => 'ISSUED',
                ]);

                // Attach transport modes
                $randomTransportModes = $transportModes->random(rand(1, 2));
                $sppd->transportModes()->attach($randomTransportModes->pluck('id')->toArray());
            }
        }
    }
}

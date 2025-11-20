<?php

namespace Database\Seeders;

use App\Models\Spt;
use App\Models\NotaDinas;
use App\Models\User;
use App\Models\Unit;
use App\Models\City;
use App\Models\OrgPlace;
use App\Services\DocumentNumberService;
use Illuminate\Database\Seeder;

class SptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notaDinas = NotaDinas::with('participants')->get();
        $users = User::all();
        $units = Unit::all();
        $cities = City::all();
        $orgPlaces = OrgPlace::all();

        if ($notaDinas->isEmpty() || $users->isEmpty() || $units->isEmpty() || $cities->isEmpty() || $orgPlaces->isEmpty()) {
            return;
        }

        foreach ($notaDinas as $notaDinas) {
            // Create SPT for each Nota Dinas
            $docNumberResult = DocumentNumberService::generate('SPT', $notaDinas->requesting_unit_id, now(), [
                'nota_dinas_id' => $notaDinas->id,
            ], $users->first()->id);

            $spt = Spt::create([
                'doc_no' => $docNumberResult['number'],
                'number_is_manual' => false,
                'number_format_id' => $docNumberResult['format']->id ?? null,
                'number_sequence_id' => $docNumberResult['sequence']->id ?? null,
                'number_scope_unit_id' => $notaDinas->requesting_unit_id,
                'nota_dinas_id' => $notaDinas->id,
                'spt_date' => now(),
                'signed_by_user_id' => $users->first()->id,
                'assignment_title' => 'Tugas Perjalanan Dinas',
                'origin_place_id' => $notaDinas->origin_place_id ?? $orgPlaces->first()->id,
                'destination_city_id' => $notaDinas->destination_city_id,
                'start_date' => $notaDinas->start_date,
                'end_date' => $notaDinas->end_date,
                'days_count' => 3,
                'funding_source' => 'APBD',
                'status' => 'SIGNED',
                'notes' => 'SPT untuk Nota Dinas ' . $notaDinas->doc_no,
            ]);

            // Create SPT members from Nota Dinas participants
            foreach ($notaDinas->participants as $participant) {
                $spt->members()->create([
                    'user_id' => $participant->user_id,
                ]);
            }
        }
    }
}

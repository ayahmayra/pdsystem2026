<?php

namespace Database\Seeders;

use App\Models\TravelRoute;
use App\Models\OrgPlace;
use App\Models\TransportMode;
use Illuminate\Database\Seeder;

class TravelRoutesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data yang sudah ada
        $bengkalis = OrgPlace::where('name', 'Kedudukan Bengkalis')->first();
        $pekanbaru = OrgPlace::where('name', 'Kantor Pusat Pekanbaru')->first();
        $jakarta = OrgPlace::where('name', 'Kedudukan Jakarta')->first();
        
        $airMode = TransportMode::where('code', 'AIR')->first();
        $roadPublicMode = TransportMode::where('code', 'ROAD_PUBLIC')->first();
        $roadOfficialMode = TransportMode::where('code', 'ROAD_OFFICIAL')->first();
        $seaMode = TransportMode::where('code', 'SEA')->first();

        if ($bengkalis && $pekanbaru && $airMode && $roadPublicMode) {
            // Rute Bengkalis → Pekanbaru
            TravelRoute::create([
                'origin_place_id' => $bengkalis->id,
                'destination_place_id' => $pekanbaru->id,
                'mode_id' => $airMode->id,
                'is_roundtrip' => true,
                'class' => 'ECONOMY',
            ]);

            TravelRoute::create([
                'origin_place_id' => $bengkalis->id,
                'destination_place_id' => $pekanbaru->id,
                'mode_id' => $roadPublicMode->id,
                'is_roundtrip' => false,
                'class' => null,
            ]);
        }

        if ($pekanbaru && $jakarta && $airMode) {
            // Rute Pekanbaru → Jakarta
            TravelRoute::create([
                'origin_place_id' => $pekanbaru->id,
                'destination_place_id' => $jakarta->id,
                'mode_id' => $airMode->id,
                'is_roundtrip' => true,
                'class' => 'ECONOMY',
            ]);

            TravelRoute::create([
                'origin_place_id' => $pekanbaru->id,
                'destination_place_id' => $jakarta->id,
                'mode_id' => $airMode->id,
                'is_roundtrip' => true,
                'class' => 'BUSINESS',
            ]);
        }

        if ($bengkalis && $jakarta && $seaMode) {
            // Rute Bengkalis → Jakarta (via laut)
            TravelRoute::create([
                'origin_place_id' => $bengkalis->id,
                'destination_place_id' => $jakarta->id,
                'mode_id' => $seaMode->id,
                'is_roundtrip' => false,
                'class' => null,
            ]);
        }
    }
}

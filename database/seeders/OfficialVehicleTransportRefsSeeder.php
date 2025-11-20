<?php

namespace Database\Seeders;

use App\Models\OfficialVehicleTransportRef;
use App\Models\OrgPlace;
use App\Models\District;
use Illuminate\Database\Seeder;

class OfficialVehicleTransportRefsSeeder extends Seeder
{
    public function run(): void
    {
        $orgPlaces = OrgPlace::all();
        $districts = District::all();
        $contexts = ['Kedudukan Bengkalis', 'Kedudukan Duri'];

        if ($orgPlaces->isEmpty() || $districts->isEmpty()) {
            return;
        }

        // Base prices untuk jarak berbeda
        $basePrices = [
            'very_short' => 10000,
            'short' => 15000,
            'medium' => 25000,
            'long' => 35000,
        ];

        // Multiplier berdasarkan nama kecamatan (simulasi jarak)
        $distanceMultipliers = [
            'Pekanbaru Kota' => 1.0,
            'Pekanbaru Selatan' => 1.2,
            'Pekanbaru Barat' => 1.3,
            'Pekanbaru Utara' => 1.4,
            'Pekanbaru Timur' => 1.1,
            'Tenayan Raya' => 1.5,
            'Marpoyan Damai' => 1.2,
            'Rumbai' => 1.6,
            'Rumbai Pesisir' => 1.8,
            'Bukit Raya' => 1.3,
            'Sail' => 1.1,
            'Lima Puluh' => 1.4,
            'Payung Sekaki' => 1.2,
            'Senapelan' => 1.0,
            'Sukajadi' => 1.1,
        ];

        foreach ($orgPlaces as $orgPlace) {
            foreach ($districts as $district) {
                // Skip jika origin dan destination sama
                if ($orgPlace->district_id === $district->id) {
                    continue;
                }

                foreach ($contexts as $context) {
                    $districtName = strtoupper($district->name);
                    $multiplier = 1.0;

                    // Tentukan multiplier berdasarkan nama kecamatan
                    foreach ($distanceMultipliers as $key => $value) {
                        if (str_contains($districtName, strtoupper($key))) {
                            $multiplier = $value;
                            break;
                        }
                    }

                    // Tentukan base price berdasarkan multiplier
                    if ($multiplier <= 1.1) {
                        $basePrice = $basePrices['very_short'];
                    } elseif ($multiplier <= 1.3) {
                        $basePrice = $basePrices['short'];
                    } elseif ($multiplier <= 1.5) {
                        $basePrice = $basePrices['medium'];
                    } else {
                        $basePrice = $basePrices['long'];
                    }

                    $finalPrice = $basePrice * $multiplier;

                    OfficialVehicleTransportRef::create([
                        'origin_place_id' => $orgPlace->id,
                        'destination_district_id' => $district->id,
                        'pp_amount' => $finalPrice,
                        'context' => $context,
                    ]);
                }
            }
        }
    }
}

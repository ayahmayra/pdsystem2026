<?php

namespace Database\Seeders;

use App\Models\IntraProvinceTransportRef;
use App\Models\OrgPlace;
use App\Models\City;
use Illuminate\Database\Seeder;

class IntraProvinceTransportRefsSeeder extends Seeder
{
    public function run(): void
    {
        $orgPlaces = OrgPlace::all();
        $cities = City::all();

        if ($orgPlaces->isEmpty() || $cities->isEmpty()) {
            return;
        }

        // Base prices untuk jarak berbeda
        $basePrices = [
            'very_short' => 25000,
            'short' => 50000,
            'medium' => 75000,
            'long' => 100000,
        ];

        // Multiplier berdasarkan nama kota (simulasi jarak)
        $distanceMultipliers = [
            'PEKANBARU' => 1.0,
            'KAMPAR' => 1.2,
            'INDRAGIRI HULU' => 1.5,
            'INDRAGIRI HILIR' => 1.8,
            'BENGKALIS' => 1.3,
            'ROKAN HULU' => 1.4,
            'ROKAN HILIR' => 1.6,
            'SIAK' => 1.1,
            'PELALAWAN' => 1.2,
            'KUANTAN SINGINGI' => 1.7,
            'MERANTI' => 2.0,
        ];

        foreach ($orgPlaces as $orgPlace) {
            foreach ($cities as $city) {
                // Skip jika origin dan destination sama
                if ($orgPlace->city_id === $city->id) {
                    continue;
                }

                $cityName = strtoupper($city->name);
                $multiplier = 1.0;

                // Tentukan multiplier berdasarkan nama kota
                foreach ($distanceMultipliers as $key => $value) {
                    if (str_contains($cityName, $key)) {
                        $multiplier = $value;
                        break;
                    }
                }

                // Tentukan base price berdasarkan multiplier
                if ($multiplier <= 1.1) {
                    $basePrice = $basePrices['very_short'];
                } elseif ($multiplier <= 1.3) {
                    $basePrice = $basePrices['short'];
                } elseif ($multiplier <= 1.6) {
                    $basePrice = $basePrices['medium'];
                } else {
                    $basePrice = $basePrices['long'];
                }

                $finalPrice = $basePrice * $multiplier;

                IntraProvinceTransportRef::create([
                    'origin_place_id' => $orgPlace->id,
                    'destination_city_id' => $city->id,
                    'pp_amount' => $finalPrice,
                ]);
            }
        }
    }
}

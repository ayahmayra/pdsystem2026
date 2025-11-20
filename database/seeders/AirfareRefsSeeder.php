<?php

namespace Database\Seeders;

use App\Models\AirfareRef;
use App\Models\City;
use Illuminate\Database\Seeder;

class AirfareRefsSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil kota Pekanbaru sebagai origin (biasanya)
        $pekanbaru = City::where('name', 'LIKE', '%Pekanbaru%')->first();
        
        if (!$pekanbaru) {
            // Jika tidak ada Pekanbaru, ambil kota pertama
            $pekanbaru = City::first();
        }

        if (!$pekanbaru) {
            return; // Tidak ada data kota
        }

        // Ambil semua kota lain sebagai destination
        $destinationCities = City::where('id', '!=', $pekanbaru->id)->get();

        // Base prices untuk economy dan business class
        $basePrices = [
            'ECONOMY' => [
                'base' => 800, // Rp 800.000
                'multiplier' => 1.0,
            ],
            'BUSINESS' => [
                'base' => 1200, // Rp 1.200.000
                'multiplier' => 1.5,
            ],
        ];

        foreach ($destinationCities as $destinationCity) {
            foreach (['ECONOMY', 'BUSINESS'] as $class) {
                $basePrice = $basePrices[$class]['base'];
                $multiplier = $basePrices[$class]['multiplier'];
                
                // Modifikasi harga berdasarkan jarak (simulasi)
                // Kota yang lebih jauh akan lebih mahal
                $distanceMultiplier = 1.0;
                if (str_contains(strtolower($destinationCity->name), 'jakarta')) {
                    $distanceMultiplier = 1.3; // Jakarta lebih jauh
                } elseif (str_contains(strtolower($destinationCity->name), 'surabaya')) {
                    $distanceMultiplier = 1.2; // Surabaya cukup jauh
                } elseif (str_contains(strtolower($destinationCity->name), 'medan')) {
                    $distanceMultiplier = 1.1; // Medan agak jauh
                }

                $finalPrice = $basePrice * $multiplier * $distanceMultiplier * 1000; // Konversi ke rupiah

                AirfareRef::create([
                    'origin_city_id' => $pekanbaru->id,
                    'destination_city_id' => $destinationCity->id,
                    'class' => $class,
                    'pp_estimate' => $finalPrice,
                ]);
            }
        }
    }
}

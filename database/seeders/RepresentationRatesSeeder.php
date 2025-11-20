<?php

namespace Database\Seeders;

use App\Models\RepresentationRate;
use App\Models\TravelGrade;
use Illuminate\Database\Seeder;

class RepresentationRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hanya untuk Bupati dan Eselon II / Anggota DPRD
        $eligibleGrades = TravelGrade::whereIn('code', ['BUP', 'ESELON2_ANGGOTA_DPRD'])->get();

        // Tarif representasi per tingkatan (dalam ribuan rupiah)
        $representationRates = [
            'BUP' => [
                'luar_kota' => 500, // Rp 500.000
                'dalam_kota_gt8h' => 250, // Rp 250.000
            ],
            'ESELON2_ANGGOTA_DPRD' => [
                'luar_kota' => 400, // Rp 400.000
                'dalam_kota_gt8h' => 200, // Rp 200.000
            ],
        ];

        foreach ($eligibleGrades as $travelGrade) {
            $rates = $representationRates[$travelGrade->code];
            
            RepresentationRate::create([
                'travel_grade_id' => $travelGrade->id,
                'satuan' => 'OH',
                'luar_kota' => $rates['luar_kota'] * 1000, // Konversi ke rupiah
                'dalam_kota_gt8h' => $rates['dalam_kota_gt8h'] * 1000,
            ]);
        }
    }
}

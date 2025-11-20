<?php

namespace Database\Seeders;

use App\Models\LodgingCap;
use App\Models\Province;
use App\Models\TravelGrade;
use Illuminate\Database\Seeder;

class LodgingCapsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = Province::all();
        $travelGrades = TravelGrade::all();

        // Tarif dasar per tingkatan (dalam ribuan rupiah)
        $baseCaps = [
            'BUP' => 2000, // Rp 2.000.000
            'ESELON2_ANGGOTA_DPRD' => 1500, // Rp 1.500.000
            'ESELON3_GOL4' => 1200, // Rp 1.200.000
            'ESELON4_GOL3_2_1_PPPK_NONASN' => 1000, // Rp 1.000.000
        ];

        foreach ($provinces as $province) {
            foreach ($travelGrades as $travelGrade) {
                $baseCap = $baseCaps[$travelGrade->code];
                
                // Modifikasi tarif berdasarkan provinsi (DKI Jakarta lebih tinggi)
                $multiplier = $province->kemendagri_code === '31' ? 1.3 : 1.0;
                
                // Provinsi tertentu dengan tarif lebih tinggi
                if (in_array($province->kemendagri_code, ['32', '33', '34', '35'])) { // Jawa Barat, DI Yogyakarta, Jawa Tengah, Jawa Timur
                    $multiplier = 1.1;
                }
                
                LodgingCap::create([
                    'province_id' => $province->id,
                    'travel_grade_id' => $travelGrade->id,
                    'cap_amount' => $baseCap * $multiplier * 1000, // Konversi ke rupiah
                ]);
            }
        }
    }
}

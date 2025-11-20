<?php

namespace Database\Seeders;

use App\Models\PerdiemRate;
use App\Models\Province;
use App\Models\TravelGrade;
use Illuminate\Database\Seeder;

class PerdiemRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data tarif uang harian berdasarkan dokumen yang diberikan
        $perdiemRates = [
            ['name' => 'ACEH', 'luar_kota' => 360000, 'dalam_kota_gt8h' => 140000, 'diklat' => 110000],
            ['name' => 'SUMATERA UTARA', 'luar_kota' => 370000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'RIAU', 'luar_kota' => 370000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'KEPULAUAN RIAU', 'luar_kota' => 370000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'JAMBI', 'luar_kota' => 370000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'SUMATERA BARAT', 'luar_kota' => 380000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'SUMATERA SELATAN', 'luar_kota' => 380000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'LAMPUNG', 'luar_kota' => 380000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'BENGKULU', 'luar_kota' => 380000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'BANGKA BELITUNG', 'luar_kota' => 410000, 'dalam_kota_gt8h' => 160000, 'diklat' => 120000],
            ['name' => 'BANTEN', 'luar_kota' => 370000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'JAWA BARAT', 'luar_kota' => 430000, 'dalam_kota_gt8h' => 170000, 'diklat' => 130000],
            ['name' => 'D.K.I. JAKARTA', 'luar_kota' => 530000, 'dalam_kota_gt8h' => 210000, 'diklat' => 160000],
            ['name' => 'JAWA TENGAH', 'luar_kota' => 370000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'D.I. YOGYAKARTA', 'luar_kota' => 420000, 'dalam_kota_gt8h' => 170000, 'diklat' => 130000],
            ['name' => 'JAWA TIMUR', 'luar_kota' => 410000, 'dalam_kota_gt8h' => 160000, 'diklat' => 120000],
            ['name' => 'BALI', 'luar_kota' => 480000, 'dalam_kota_gt8h' => 190000, 'diklat' => 140000],
            ['name' => 'NUSA TENGGARA BARAT', 'luar_kota' => 440000, 'dalam_kota_gt8h' => 180000, 'diklat' => 130000],
            ['name' => 'NUSA TENGGARA TIMUR', 'luar_kota' => 430000, 'dalam_kota_gt8h' => 170000, 'diklat' => 130000],
            ['name' => 'KALIMANTAN BARAT', 'luar_kota' => 380000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'KALIMANTAN TENGAH', 'luar_kota' => 360000, 'dalam_kota_gt8h' => 140000, 'diklat' => 110000],
            ['name' => 'KALIMANTAN SELATAN', 'luar_kota' => 380000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'KALIMANTAN TIMUR', 'luar_kota' => 430000, 'dalam_kota_gt8h' => 170000, 'diklat' => 130000],
            ['name' => 'KALIMANTAN UTARA', 'luar_kota' => 430000, 'dalam_kota_gt8h' => 170000, 'diklat' => 130000],
            ['name' => 'SULAWESI UTARA', 'luar_kota' => 370000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'GORONTALO', 'luar_kota' => 370000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'SULAWESI BARAT', 'luar_kota' => 410000, 'dalam_kota_gt8h' => 160000, 'diklat' => 120000],
            ['name' => 'SULAWESI SELATAN', 'luar_kota' => 430000, 'dalam_kota_gt8h' => 170000, 'diklat' => 130000],
            ['name' => 'SULAWESI TENGAH', 'luar_kota' => 370000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'SULAWESI TENGGARA', 'luar_kota' => 380000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'MALUKU', 'luar_kota' => 380000, 'dalam_kota_gt8h' => 150000, 'diklat' => 110000],
            ['name' => 'MALUKU UTARA', 'luar_kota' => 430000, 'dalam_kota_gt8h' => 170000, 'diklat' => 130000],
            ['name' => 'PAPUA', 'luar_kota' => 580000, 'dalam_kota_gt8h' => 230000, 'diklat' => 170000],
            ['name' => 'PAPUA BARAT', 'luar_kota' => 480000, 'dalam_kota_gt8h' => 190000, 'diklat' => 140000],
            ['name' => 'PAPUA BARAT DAYA', 'luar_kota' => 480000, 'dalam_kota_gt8h' => 190000, 'diklat' => 140000],
            ['name' => 'PAPUA TENGAH', 'luar_kota' => 580000, 'dalam_kota_gt8h' => 230000, 'diklat' => 170000],
            ['name' => 'PAPUA SELATAN', 'luar_kota' => 580000, 'dalam_kota_gt8h' => 230000, 'diklat' => 170000],
            ['name' => 'PAPUA PEGUNUNGAN', 'luar_kota' => 580000, 'dalam_kota_gt8h' => 230000, 'diklat' => 170000],
        ];

        // Get all travel grades
        $travelGrades = TravelGrade::all();

        foreach ($perdiemRates as $rateData) {
            // Cari provinsi berdasarkan nama
            $province = Province::where('name', 'like', '%' . $rateData['name'] . '%')->first();
            
            if ($province) {
                // Create perdiem rate for each travel grade
                foreach ($travelGrades as $travelGrade) {
                    // Cek apakah sudah ada data untuk kombinasi provinsi dan travel grade ini
                    $existingRate = PerdiemRate::where('province_id', $province->id)
                        ->where('travel_grade_id', $travelGrade->id)
                        ->first();
                    
                    if (!$existingRate) {
                        PerdiemRate::create([
                            'province_id' => $province->id,
                            'travel_grade_id' => $travelGrade->id,
                            'satuan' => 'OH',
                            'luar_kota' => $rateData['luar_kota'],
                            'dalam_kota_gt8h' => $rateData['dalam_kota_gt8h'],
                            'diklat' => $rateData['diklat'],
                        ]);
                    }
                }
            }
        }
    }
}

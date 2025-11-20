<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvincesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = [
            ['kemendagri_code' => '11', 'name' => 'ACEH'],
            ['kemendagri_code' => '12', 'name' => 'SUMATERA UTARA'],
            ['kemendagri_code' => '14', 'name' => 'RIAU'],
            ['kemendagri_code' => '21', 'name' => 'KEPULAUAN RIAU'],
            ['kemendagri_code' => '15', 'name' => 'JAMBI'],
            ['kemendagri_code' => '13', 'name' => 'SUMATERA BARAT'],
            ['kemendagri_code' => '16', 'name' => 'SUMATERA SELATAN'],
            ['kemendagri_code' => '18', 'name' => 'LAMPUNG'],
            ['kemendagri_code' => '17', 'name' => 'BENGKULU'],
            ['kemendagri_code' => '19', 'name' => 'BANGKA BELITUNG'],
            ['kemendagri_code' => '36', 'name' => 'BANTEN'],
            ['kemendagri_code' => '32', 'name' => 'JAWA BARAT'],
            ['kemendagri_code' => '31', 'name' => 'D.K.I. JAKARTA'],
            ['kemendagri_code' => '33', 'name' => 'JAWA TENGAH'],
            ['kemendagri_code' => '34', 'name' => 'D.I. YOGYAKARTA'],
            ['kemendagri_code' => '35', 'name' => 'JAWA TIMUR'],
            ['kemendagri_code' => '51', 'name' => 'BALI'],
            ['kemendagri_code' => '52', 'name' => 'NUSA TENGGARA BARAT'],
            ['kemendagri_code' => '53', 'name' => 'NUSA TENGGARA TIMUR'],
            ['kemendagri_code' => '61', 'name' => 'KALIMANTAN BARAT'],
            ['kemendagri_code' => '62', 'name' => 'KALIMANTAN TENGAH'],
            ['kemendagri_code' => '63', 'name' => 'KALIMANTAN SELATAN'],
            ['kemendagri_code' => '64', 'name' => 'KALIMANTAN TIMUR'],
            ['kemendagri_code' => '65', 'name' => 'KALIMANTAN UTARA'],
            ['kemendagri_code' => '71', 'name' => 'SULAWESI UTARA'],
            ['kemendagri_code' => '75', 'name' => 'GORONTALO'],
            ['kemendagri_code' => '76', 'name' => 'SULAWESI BARAT'],
            ['kemendagri_code' => '73', 'name' => 'SULAWESI SELATAN'],
            ['kemendagri_code' => '72', 'name' => 'SULAWESI TENGAH'],
            ['kemendagri_code' => '74', 'name' => 'SULAWESI TENGGARA'],
            ['kemendagri_code' => '81', 'name' => 'MALUKU'],
            ['kemendagri_code' => '82', 'name' => 'MALUKU UTARA'],
            ['kemendagri_code' => '94', 'name' => 'PAPUA'],
            ['kemendagri_code' => '91', 'name' => 'PAPUA BARAT'],
            ['kemendagri_code' => '92', 'name' => 'PAPUA BARAT DAYA'],
            ['kemendagri_code' => '93', 'name' => 'PAPUA TENGAH'],
            ['kemendagri_code' => '95', 'name' => 'PAPUA SELATAN'],
            ['kemendagri_code' => '96', 'name' => 'PAPUA PEGUNUNGAN'],
        ];

        foreach ($provinces as $province) {
            // Cek apakah provinsi sudah ada
            $existingProvince = Province::where('kemendagri_code', $province['kemendagri_code'])->first();
            
            if (!$existingProvince) {
                Province::create($province);
            }
        }
    }
}

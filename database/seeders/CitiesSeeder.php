<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            // RIAU (14)
            ['kemendagri_code' => '1401', 'province_code' => '14', 'name' => 'KAMPAR', 'type' => 'KAB'],
            ['kemendagri_code' => '1402', 'province_code' => '14', 'name' => 'INDRAGIRI HULU', 'type' => 'KAB'],
            ['kemendagri_code' => '1403', 'province_code' => '14', 'name' => 'BENGKALIS', 'type' => 'KAB'],
            ['kemendagri_code' => '1404', 'province_code' => '14', 'name' => 'INDRAGIRI HILIR', 'type' => 'KAB'],
            ['kemendagri_code' => '1405', 'province_code' => '14', 'name' => 'PELALAWAN', 'type' => 'KAB'],
            ['kemendagri_code' => '1406', 'province_code' => '14', 'name' => 'ROKAN HULU', 'type' => 'KAB'],
            ['kemendagri_code' => '1407', 'province_code' => '14', 'name' => 'ROKAN HILIR', 'type' => 'KAB'],
            ['kemendagri_code' => '1408', 'province_code' => '14', 'name' => 'SIAK', 'type' => 'KAB'],
            ['kemendagri_code' => '1409', 'province_code' => '14', 'name' => 'KUANTAN SINGINGI', 'type' => 'KAB'],
            ['kemendagri_code' => '1410', 'province_code' => '14', 'name' => 'KEPULAUAN MERANTI', 'type' => 'KAB'],
            ['kemendagri_code' => '1471', 'province_code' => '14', 'name' => 'PEKANBARU', 'type' => 'KOTA'],
            ['kemendagri_code' => '1473', 'province_code' => '14', 'name' => 'DUMAI', 'type' => 'KOTA'],
            
            // DKI JAKARTA (31)
            ['kemendagri_code' => '3171', 'province_code' => '31', 'name' => 'JAKARTA SELATAN', 'type' => 'KOTA'],
            ['kemendagri_code' => '3172', 'province_code' => '31', 'name' => 'JAKARTA TIMUR', 'type' => 'KOTA'],
            ['kemendagri_code' => '3173', 'province_code' => '31', 'name' => 'JAKARTA PUSAT', 'type' => 'KOTA'],
            ['kemendagri_code' => '3174', 'province_code' => '31', 'name' => 'JAKARTA BARAT', 'type' => 'KOTA'],
            ['kemendagri_code' => '3175', 'province_code' => '31', 'name' => 'JAKARTA UTARA', 'type' => 'KOTA'],
        ];

        foreach ($cities as $city) {
            $province = Province::where('kemendagri_code', $city['province_code'])->first();
            if ($province) {
                City::create([
                    'kemendagri_code' => $city['kemendagri_code'],
                    'province_id' => $province->id,
                    'name' => $city['name'],
                    'type' => $city['type'],
                ]);
            }
        }
    }
}

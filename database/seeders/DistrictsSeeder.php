<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            // BENGKALIS (1403)
            ['kemendagri_code' => '140301', 'city_code' => '1403', 'name' => 'BENGKALIS'],
            ['kemendagri_code' => '140302', 'city_code' => '1403', 'name' => 'BANTAN'],
            ['kemendagri_code' => '140303', 'city_code' => '1403', 'name' => 'BUKIT BATU'],
            ['kemendagri_code' => '140309', 'city_code' => '1403', 'name' => 'MANDAU'],
            ['kemendagri_code' => '140310', 'city_code' => '1403', 'name' => 'RUPAT'],
            ['kemendagri_code' => '140311', 'city_code' => '1403', 'name' => 'RUPAT UTARA'],
            ['kemendagri_code' => '140312', 'city_code' => '1403', 'name' => 'SIAK KECIL'],
            ['kemendagri_code' => '140313', 'city_code' => '1403', 'name' => 'PINGGIR'],
            
            // PEKANBARU (1471)
            ['kemendagri_code' => '147101', 'city_code' => '1471', 'name' => 'PEKANBARU KOTA'],
            ['kemendagri_code' => '147102', 'city_code' => '1471', 'name' => 'SUKAJADI'],
            ['kemendagri_code' => '147103', 'city_code' => '1471', 'name' => 'SENAPELAN'],
            ['kemendagri_code' => '147104', 'city_code' => '1471', 'name' => 'RUMBAI PESISIR'],
            ['kemendagri_code' => '147105', 'city_code' => '1471', 'name' => 'LIMAPULUH'],
            ['kemendagri_code' => '147106', 'city_code' => '1471', 'name' => 'SAIL'],
            ['kemendagri_code' => '147107', 'city_code' => '1471', 'name' => 'BUKIT RAYA'],
            ['kemendagri_code' => '147108', 'city_code' => '1471', 'name' => 'TAMPAN'],
            ['kemendagri_code' => '147109', 'city_code' => '1471', 'name' => 'MARPOYAN DAMAI'],
            ['kemendagri_code' => '147110', 'city_code' => '1471', 'name' => 'TENAYAN RAYA'],
            ['kemendagri_code' => '147111', 'city_code' => '1471', 'name' => 'PAYUNG SEKAKI'],
            ['kemendagri_code' => '147112', 'city_code' => '1471', 'name' => 'RUMBAI'],
        ];

        foreach ($districts as $district) {
            $city = City::where('kemendagri_code', $district['city_code'])->first();
            if ($city) {
                District::create([
                    'kemendagri_code' => $district['kemendagri_code'],
                    'city_id' => $city->id,
                    'name' => $district['name'],
                ]);
            }
        }
    }
}

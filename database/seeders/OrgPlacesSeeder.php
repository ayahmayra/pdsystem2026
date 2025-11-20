<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use App\Models\OrgPlace;
use Illuminate\Database\Seeder;

class OrgPlacesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orgPlaces = [
            // Kedudukan Bengkalis
            [
                'name' => 'Kedudukan Bengkalis',
                'city_code' => '1403',
                'district_code' => '140301',
                'is_org_headquarter' => false,
            ],
            // Kedudukan Duri
            [
                'name' => 'Kedudukan Duri',
                'city_code' => '1403',
                'district_code' => '140309',
                'is_org_headquarter' => false,
            ],
            // Kantor Pusat Pekanbaru
            [
                'name' => 'Kantor Pusat Pekanbaru',
                'city_code' => '1471',
                'district_code' => '147101',
                'is_org_headquarter' => true,
            ],
            // Kedudukan Jakarta
            [
                'name' => 'Kedudukan Jakarta',
                'city_code' => '3173',
                'district_code' => null,
                'is_org_headquarter' => false,
            ],
        ];

        foreach ($orgPlaces as $orgPlace) {
            $city = City::where('kemendagri_code', $orgPlace['city_code'])->first();
            $district = null;
            
            if ($orgPlace['district_code']) {
                $district = District::where('kemendagri_code', $orgPlace['district_code'])->first();
            }
            
            if ($city) {
                OrgPlace::create([
                    'name' => $orgPlace['name'],
                    'city_id' => $city->id,
                    'district_id' => $district?->id,
                    'is_org_headquarter' => $orgPlace['is_org_headquarter'],
                ]);
            }
        }
    }
}

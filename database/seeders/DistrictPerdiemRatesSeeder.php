<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DistrictPerdiemRate;
use App\Models\District;

class DistrictPerdiemRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data untuk KEDUDUKAN BENGKALIS
        $bengkalisData = [
            'Kecamatan Bengkalis' => 150000,
            'Kecamatan Bantan' => 150000,
            'Kecamatan Bukit Batu' => 150000,
            'Kecamatan Siak Kecil' => 150000,
            'Kecamatan Bandar Laksamana' => 150000,
            'Kecamatan Rupat' => 370000,
            'Kecamatan Rupat Utara' => 370000,
            'Kecamatan Mandau' => 370000,
            'Kecamatan Pinggir' => 370000,
            'Kecamatan Bathin Solapan' => 370000,
            'Kecamatan Talang Muandau' => 370000,
        ];

        // Data untuk KEDUDUKAN DURI
        $duriData = [
            'Kecamatan Bengkalis' => 370000,
            'Kecamatan Bantan' => 370000,
            'Kecamatan Bukit Batu' => 200000,
            'Kecamatan Siak Kecil' => 200000,
            'Kecamatan Bandar Laksamana' => 200000,
            'Kecamatan Rupat' => 250000,
            'Kecamatan Rupat Utara' => 300000,
            'Kecamatan Mandau' => 150000,
            'Kecamatan Pinggir' => 150000,
            'Kecamatan Talang Muandau' => 150000,
        ];

        // Insert data untuk KEDUDUKAN BENGKALIS
        foreach ($bengkalisData as $districtName => $dailyRate) {
            $district = District::where('name', 'like', '%' . str_replace('Kecamatan ', '', $districtName) . '%')->first();
            
            if ($district) {
                DistrictPerdiemRate::updateOrCreate(
                    [
                        'org_place_name' => 'BENGKALIS',
                        'district_id' => $district->id,
                    ],
                    [
                        'unit' => 'OH',
                        'daily_rate' => $dailyRate,
                        'is_active' => true,
                    ]
                );
            }
        }

        // Insert data untuk KEDUDUKAN DURI
        foreach ($duriData as $districtName => $dailyRate) {
            $district = District::where('name', 'like', '%' . str_replace('Kecamatan ', '', $districtName) . '%')->first();
            
            if ($district) {
                DistrictPerdiemRate::updateOrCreate(
                    [
                        'org_place_name' => 'DURI',
                        'district_id' => $district->id,
                    ],
                    [
                        'unit' => 'OH',
                        'daily_rate' => $dailyRate,
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('District Perdiem Rates seeded successfully!');
    }
}

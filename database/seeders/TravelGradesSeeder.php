<?php

namespace Database\Seeders;

use App\Models\TravelGrade;
use Illuminate\Database\Seeder;

class TravelGradesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $travelGrades = [
            [
                'code' => 'BUP',
                'name' => 'Bupati',
            ],
            [
                'code' => 'ESELON2_ANGGOTA_DPRD',
                'name' => 'Eselon II / Anggota DPRD',
            ],
            [
                'code' => 'ESELON3_GOL4',
                'name' => 'Eselon III / Golongan IV',
            ],
            [
                'code' => 'ESELON4_GOL3_2_1_PPPK_NONASN',
                'name' => 'Eselon IV / Golongan III-II-I / PPPK / Non ASN',
            ],
        ];

        foreach ($travelGrades as $grade) {
            TravelGrade::create($grade);
        }
    }
}

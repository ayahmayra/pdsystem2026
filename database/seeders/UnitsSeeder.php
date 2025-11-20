<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create parent unit first
        $parentUnit = Unit::create([
            'code' => 'BPKAD',
            'name' => 'Badan Pengelola Keuangan dan Aset Daerah',
            'parent_id' => null,
        ]);

        // Create child units
        $childUnits = [
            ['code' => 'BPKAD-KEU', 'name' => 'Bidang Perbendaharaan'],
            ['code' => 'BPKAD-ANG', 'name' => 'Bidang Anggaran'],
            ['code' => 'BPKAD-ASET', 'name' => 'Bidang Aset'],
        ];

        foreach ($childUnits as $childUnit) {
            Unit::create([
                'code' => $childUnit['code'],
                'name' => $childUnit['name'],
                'parent_id' => $parentUnit->id,
            ]);
        }
    }
}

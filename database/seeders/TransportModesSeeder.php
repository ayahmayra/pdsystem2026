<?php

namespace Database\Seeders;

use App\Models\TransportMode;
use Illuminate\Database\Seeder;

class TransportModesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transportModes = [
            ['code' => 'AIR', 'name' => 'Pesawat Terbang'],
            ['code' => 'SEA', 'name' => 'Kapal Laut'],
            ['code' => 'ROAD_PUBLIC', 'name' => 'Angkutan Umum Darat'],
            ['code' => 'ROAD_OFFICIAL', 'name' => 'Kendaraan Dinas'],
            ['code' => 'RORO', 'name' => 'Kapal RORO'],
            ['code' => 'TOLL', 'name' => 'Tol'],
            ['code' => 'PARKIR_INAP', 'name' => 'Parkir & Inap'],
            ['code' => 'TAKSI', 'name' => 'Taksi'],
        ];

        foreach ($transportModes as $mode) {
            TransportMode::create($mode);
        }
    }
}

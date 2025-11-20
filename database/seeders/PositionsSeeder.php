<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Echelon;
use Illuminate\Database\Seeder;

class PositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $echelons = Echelon::all();
        $echelonMap = $echelons->keyBy('code');

        $positions = [
            // Kepala OPD - ESELON 2B
            ['name' => 'Kepala Badan Pengelola Keuangan dan Aset Daerah', 'echelon_code' => 'II.b'],
            
            // Kepala Bidang - ESELON 3A
            ['name' => 'Kepala Bidang Perbendaharaan', 'echelon_code' => 'III.a'],
            ['name' => 'Kepala Bidang Anggaran', 'echelon_code' => 'III.a'],
            ['name' => 'Kepala Bidang Aset', 'echelon_code' => 'III.a'],
            
            // Kepala Seksi - ESELON 3B
            ['name' => 'Kepala Seksi Perbendaharaan', 'echelon_code' => 'III.b'],
            ['name' => 'Kepala Seksi Anggaran', 'echelon_code' => 'III.b'],
            ['name' => 'Kepala Seksi Aset', 'echelon_code' => 'III.b'],
            
            // Staff - ESELON 4A
            ['name' => 'Staff Perbendaharaan', 'echelon_code' => 'IV.a'],
            ['name' => 'Staff Anggaran', 'echelon_code' => 'IV.a'],
            ['name' => 'Staff Aset', 'echelon_code' => 'IV.a'],
            
            // Staff - ESELON 4B
            ['name' => 'Staff Ahli Pertama', 'echelon_code' => 'IV.b'],
            ['name' => 'Staff Ahli Muda', 'echelon_code' => 'IV.b'],
            
            // Staff - ESELON 5A
            ['name' => 'Staff Pelaksana', 'echelon_code' => 'V.a'],
            ['name' => 'Staff Pelaksana Lanjutan', 'echelon_code' => 'V.a'],
            
            // Staff - ESELON 5B
            ['name' => 'Staff Pelaksana Pemula', 'echelon_code' => 'V.b'],
            
            // Non Eselon
            ['name' => 'Staff Non Eselon', 'echelon_code' => 'NE'],
            ['name' => 'Pembantu Umum', 'echelon_code' => 'NE'],
            ['name' => 'Juru Tulis', 'echelon_code' => 'NE'],
        ];

        foreach ($positions as $position) {
            $echelon = $echelonMap->get($position['echelon_code']);
            
            Position::create([
                'name' => $position['name'],
                'echelon_id' => $echelon ? $echelon->id : null,
            ]);
        }
    }
}

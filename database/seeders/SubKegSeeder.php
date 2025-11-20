<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubKeg;
use App\Models\Unit;
use App\Models\User;

class SubKegSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first unit as default (you can modify this as needed)
        $defaultUnit = Unit::first();
        
        if (!$defaultUnit) {
            $this->command->error('No units found. Please run UnitSeeder first.');
            return;
        }

        // Get some users to assign as PPTK
        $users = User::take(3)->get();
        $pptkUsers = [];
        foreach ($users as $user) {
            $pptkUsers[] = $user->id;
        }

        $subKegiatanData = [
            [
                'kode_subkeg' => '5.02.02.2.01.0001',
                'nama_subkeg' => 'Koordinasi dan Penyusunan KUA dan PPAS',
                'pagu' => 464124308,
                'id_unit' => $defaultUnit->id,
                'pptk_user_id' => $pptkUsers[0] ?? null,
            ],
            [
                'kode_subkeg' => '5.02.02.2.02.0001',
                'nama_subkeg' => 'Koordinasi, Penyusunan dan Verifikasi RKA SKPD',
                'pagu' => 10894910615,
                'id_unit' => $defaultUnit->id,
                'pptk_user_id' => $pptkUsers[1] ?? null,
            ],
            [
                'kode_subkeg' => '5.02.03.2.01.0001',
                'nama_subkeg' => 'Penatausahaan Pembiayaan Daerah',
                'pagu' => 703674000000, // 7,03674E+11 converted to regular number
                'id_unit' => $defaultUnit->id,
                'pptk_user_id' => $pptkUsers[2] ?? null,
            ],
            [
                'kode_subkeg' => '5.02.04.2.01.0001',
                'nama_subkeg' => 'Penyusunan Standar Harga',
                'pagu' => 1500000000,
                'id_unit' => $defaultUnit->id,
                'pptk_user_id' => $pptkUsers[0] ?? null,
            ],
            [
                'kode_subkeg' => '5.02.05.2.01.0001',
                'nama_subkeg' => 'Penyediaan Gaji dan Tunjangan ASN',
                'pagu' => 25000000000,
                'id_unit' => $defaultUnit->id,
                'pptk_user_id' => $pptkUsers[1] ?? null,
            ],
            [
                'kode_subkeg' => '5.02.06.2.01.0001',
                'nama_subkeg' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan',
                'pagu' => 5000000000,
                'id_unit' => $defaultUnit->id,
                'pptk_user_id' => $pptkUsers[2] ?? null,
            ],
            [
                'kode_subkeg' => '5.02.07.2.01.0001',
                'nama_subkeg' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik',
                'pagu' => 3000000000,
                'id_unit' => $defaultUnit->id,
                'pptk_user_id' => $pptkUsers[0] ?? null,
            ],
            [
                'kode_subkeg' => '5.02.08.2.01.0001',
                'nama_subkeg' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi',
                'pagu' => 2000000000,
                'id_unit' => $defaultUnit->id,
                'pptk_user_id' => $pptkUsers[1] ?? null,
            ],
        ];

        foreach ($subKegiatanData as $data) {
            SubKeg::updateOrCreate(
                ['kode_subkeg' => $data['kode_subkeg']],
                $data
            );
        }

        $this->command->info('Sub Kegiatan seeded successfully!');
    }
}

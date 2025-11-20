<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubKeg;
use App\Models\RekeningBelanja;

class RekeningBelanjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil beberapa sub kegiatan untuk diisi rekening belanjanya
        $subKegs = SubKeg::take(3)->get();
        
        if ($subKegs->isEmpty()) {
            $this->command->info('Tidak ada Sub Kegiatan yang ditemukan. Jalankan SubKegSeeder terlebih dahulu.');
            return;
        }

        $rekeningBelanjaData = [
            // Contoh rekening belanja untuk sub kegiatan pertama
            [
                'kode_rekening' => '5.1.02.01.01.0001',
                'nama_rekening' => 'Belanja Pegawai - Gaji Pokok PNS',
                'pagu' => 500000000,
                'keterangan' => 'Gaji pokok untuk pegawai negeri sipil',
            ],
            [
                'kode_rekening' => '5.1.02.01.01.0002',
                'nama_rekening' => 'Belanja Pegawai - Tunjangan Kinerja',
                'pagu' => 200000000,
                'keterangan' => 'Tunjangan kinerja pegawai',
            ],
            [
                'kode_rekening' => '5.1.02.01.01.0003',
                'nama_rekening' => 'Belanja Barang - Alat Tulis Kantor',
                'pagu' => 50000000,
                'keterangan' => 'Pembelian alat tulis kantor',
            ],
            [
                'kode_rekening' => '5.1.02.01.01.0004',
                'nama_rekening' => 'Belanja Barang - Bahan Habis Pakai',
                'pagu' => 75000000,
                'keterangan' => 'Bahan habis pakai untuk operasional',
            ],
            [
                'kode_rekening' => '5.1.02.01.01.0005',
                'nama_rekening' => 'Belanja Modal - Peralatan Kantor',
                'pagu' => 300000000,
                'keterangan' => 'Pembelian peralatan kantor',
            ],
        ];

        foreach ($subKegs as $index => $subKeg) {
            $this->command->info("Menambahkan rekening belanja untuk Sub Kegiatan: {$subKeg->nama_subkeg}");
            
            // Ambil 2-3 rekening belanja untuk setiap sub kegiatan
            $selectedRekening = array_slice($rekeningBelanjaData, $index * 2, 2);
            
            foreach ($selectedRekening as $rekening) {
                RekeningBelanja::create([
                    'sub_keg_id' => $subKeg->id,
                    'kode_rekening' => $rekening['kode_rekening'] . '.' . ($index + 1),
                    'nama_rekening' => $rekening['nama_rekening'],
                    'pagu' => $rekening['pagu'],
                    'keterangan' => $rekening['keterangan'],
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('Rekening Belanja berhasil diisi dengan data contoh.');
    }
}

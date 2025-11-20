<?php

namespace Database\Seeders;

use App\Models\Rank;
use Illuminate\Database\Seeder;

class RanksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ranks = [
            ['code' => 'IV/e', 'name' => 'Pembina Utama'],
            ['code' => 'IV/d', 'name' => 'Pembina Utama Muda'],
            ['code' => 'IV/c', 'name' => 'Pembina Utama Muda'],
            ['code' => 'IV/b', 'name' => 'Pembina Tk.I'],
            ['code' => 'IV/a', 'name' => 'Pembina'],
            ['code' => 'III/d', 'name' => 'Penata Tk.I'],
            ['code' => 'III/c', 'name' => 'Penata'],
            ['code' => 'III/b', 'name' => 'Penata Muda Tk.I'],
            ['code' => 'III/a', 'name' => 'Penata Muda'],
            ['code' => 'II/d', 'name' => 'Pengatur Tk.I'],
            ['code' => 'II/c', 'name' => 'Pengatur'],
            ['code' => 'II/b', 'name' => 'Pengatur Muda Tk.I'],
            ['code' => 'II/a', 'name' => 'Pengatur Muda'],
            ['code' => 'I/d', 'name' => 'Juru Tk.I'],
            ['code' => 'I/c', 'name' => 'Juru'],
            ['code' => 'I/b', 'name' => 'Juru Muda Tk.I'],
            ['code' => 'I/a', 'name' => 'Juru Muda'],
            // Khusus untuk non-PNS
            ['code' => '7A', 'name' => 'ANGGOTA DPA'],
            ['code' => '6F', 'name' => 'MENTERI NEGARA'],
        ];

        foreach ($ranks as $rank) {
            Rank::create($rank);
        }
    }
}

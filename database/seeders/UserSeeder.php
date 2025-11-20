<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Unit;
use App\Models\Position;
use App\Models\Rank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first unit as fallback
        $unit = Unit::first();

        if (!$unit) {
            return;
        }

        // Get ranks and positions
        $ranks = Rank::all();
        $positions = Position::all();
        
        $rankMap = $ranks->keyBy('code');
        $positionMap = $positions->keyBy('name');

        // Data pegawai berdasarkan tabel yang diberikan
        $employees = [
            [
                'name' => 'DR. H. AREADY, S.E., M.Si.',
                'email' => 'aready@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Kepala Badan Pengelola Keuangan dan Aset Daerah',
                'rank_code' => 'IV/c',
                'nip' => '196501011985031001',
                'gender' => 'L',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'M. FIRDAUS, SE. M.Si',
                'email' => 'firdaus@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Kepala Bidang Perbendaharaan',
                'rank_code' => 'IV/b',
                'nip' => '197002021990031002',
                'gender' => 'L',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'WAN EVA YULIANI, SE., M.Si',
                'email' => 'eva@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Kepala Bidang Anggaran',
                'rank_code' => 'IV/b',
                'nip' => '197503031990032003',
                'gender' => 'P',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'SAIMAN, S.Sos, M.A.P',
                'email' => 'saiman@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Kepala Bidang Aset',
                'rank_code' => 'IV/b',
                'nip' => '197504041990031004',
                'gender' => 'L',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'AGUS SUSANTI, SE',
                'email' => 'agussusanti@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Staff Perbendaharaan',
                'rank_code' => 'III/d',
                'nip' => '197505051990032005',
                'gender' => 'P',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'IKRAMMUDDIN, S.Pi',
                'email' => 'ikrammuddin@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Staff Anggaran',
                'rank_code' => 'III/c',
                'nip' => '197506061990031006',
                'gender' => 'L',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'AGUSTIAR, S.E',
                'email' => 'agustiar@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Staff Aset',
                'rank_code' => 'III/c',
                'nip' => '197507071990031007',
                'gender' => 'L',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'MERYANA, SH',
                'email' => 'meryana@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Staff Ahli Pertama',
                'rank_code' => 'III/b',
                'nip' => '197508081990032008',
                'gender' => 'P',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'W. FARADILLA, SE',
                'email' => 'faradilla@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Staff Ahli Muda',
                'rank_code' => 'III/a',
                'nip' => '197509091990032009',
                'gender' => 'P',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'T. FAUZI, S. Sos',
                'email' => 'fauzi@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Staff Pelaksana',
                'rank_code' => 'II/d',
                'nip' => '197510101990031010',
                'gender' => 'L',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'AGUSFIAN',
                'email' => 'agusfian@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Staff Pelaksana Lanjutan',
                'rank_code' => 'II/c',
                'nip' => '197511111990031011',
                'gender' => 'L',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'MUHAMMAD IDRUS, SE',
                'email' => 'idrus@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Staff Pelaksana Pemula',
                'rank_code' => 'II/b',
                'nip' => '197512121990031012',
                'gender' => 'L',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'BUDI HERLINA, SH',
                'email' => 'budiherlina@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Staff Non Eselon',
                'rank_code' => 'II/a',
                'nip' => '197601011990032013',
                'gender' => 'P',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'BENDRI, SE.Sy, M.A.P',
                'email' => 'bendri@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Pembantu Umum',
                'rank_code' => 'I/d',
                'nip' => '197602021990031014',
                'gender' => 'L',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'ADE ZULHADI, SE',
                'email' => 'adezulhadi@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Juru Tulis',
                'rank_code' => 'I/c',
                'nip' => '197603031990031015',
                'gender' => 'L',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'HJ.NINA YULIANTI, SE',
                'email' => 'ninayulianti@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Staff Non Eselon',
                'rank_code' => 'I/b',
                'nip' => '197604041990032016',
                'gender' => 'P',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Mercedes Anthony',
                'email' => 'mercedes@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Staff Non Eselon',
                'rank_code' => 'I/a',
                'nip' => null,
                'gender' => 'P',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Winter Petty',
                'email' => 'winter@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Staff Non Eselon',
                'rank_code' => 'I/a',
                'nip' => null,
                'gender' => 'P',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ashely Anderson',
                'email' => 'ashely@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Staff Non Eselon',
                'rank_code' => 'I/a',
                'nip' => '198501012010011017',
                'gender' => 'P',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Miranda Wallace',
                'email' => 'miranda@example.com',
                'password' => Hash::make('password'),
                'unit_id' => $unit->id,
                'position_name' => 'Staff Non Eselon',
                'rank_code' => 'I/a',
                'nip' => '198502022010011018',
                'gender' => 'P',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($employees as $employeeData) {
            $position = $positionMap->get($employeeData['position_name']);
            $rank = $rankMap->get($employeeData['rank_code']);
            
            User::create([
                'name' => $employeeData['name'],
                'email' => $employeeData['email'],
                'password' => $employeeData['password'],
                'unit_id' => $employeeData['unit_id'],
                'position_id' => $position ? $position->id : null,
                'rank_id' => $rank ? $rank->id : null,
                'nip' => $employeeData['nip'],
                'gender' => $employeeData['gender'],
                'email_verified_at' => $employeeData['email_verified_at'],
            ]);
        }
    }
}

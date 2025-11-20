<?php

namespace Database\Seeders;

use App\Models\Echelon;
use Illuminate\Database\Seeder;

class EchelonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $echelons = [
            ['code' => 'I.a', 'name' => 'ESELON 1A'],
            ['code' => 'I.b', 'name' => 'ESELON 1B'],
            ['code' => 'II.a', 'name' => 'ESELON 2A'],
            ['code' => 'II.b', 'name' => 'ESELON 2B'],
            ['code' => 'III.a', 'name' => 'ESELON 3A'],
            ['code' => 'III.b', 'name' => 'ESELON 3B'],
            ['code' => 'IV.a', 'name' => 'ESELON 4A'],
            ['code' => 'IV.b', 'name' => 'ESELON 4B'],
            ['code' => 'V.a', 'name' => 'ESELON 5A'],
            ['code' => 'V.b', 'name' => 'ESELON 5B'],
            ['code' => 'NE', 'name' => 'NON ESELON'],
        ];

        foreach ($echelons as $echelon) {
            Echelon::create($echelon);
        }
    }
}

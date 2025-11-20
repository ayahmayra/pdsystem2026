<?php

namespace Database\Seeders;

use App\Models\NotaDinas;
use App\Models\NotaDinasParticipant;
use App\Models\User;
use App\Models\Unit;
use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NotaDinasSeeder extends Seeder
{
    public function run(): void
    {
        $unit = Unit::first();
        $users = User::take(5)->get();
        $city = City::first();
        if (!$unit || $users->count() < 3 || !$city) return;
        for ($i = 1; $i <= 3; $i++) {
            $nd = NotaDinas::create([
                'doc_no' => 'ND/'.Str::padLeft($i, 3, '0').'/2025',
                'number_is_manual' => false,
                'number_format_id' => null,
                'number_sequence_id' => null,
                'number_scope_unit_id' => $unit->id,
                'to_user_id' => $users[0]->id,
                'from_user_id' => $users[1]->id,
                'tembusan' => 'Tembusan dummy',
                'nd_date' => now()->subDays(10+$i),
                'sifat' => 'Penting',
                'lampiran_count' => 1,
                'hal' => 'Perihal dummy '.$i,
                'dasar' => 'Dasar dummy '.$i,
                'maksud' => 'Maksud dummy '.$i,
                'destination_city_id' => $city->id,
                'start_date' => now()->addDays($i),
                'end_date' => now()->addDays($i+2),
                'days_count' => 3,
                'spt_request_date' => now()->subDays(5+$i),
                'trip_type' => 'LUAR_DAERAH',
                'requesting_unit_id' => $unit->id,
                'signer_user_id' => $users[0]->id,
                'status' => 'DRAFT',
                'created_by' => $users[0]->id,
                'notes' => 'Catatan dummy',
            ]);
            NotaDinasParticipant::create([
                'nota_dinas_id' => $nd->id,
                'user_id' => $users[0]->id,
            ]);
            NotaDinasParticipant::create([
                'nota_dinas_id' => $nd->id,
                'user_id' => $users[1]->id,
            ]);
        }
    }
}

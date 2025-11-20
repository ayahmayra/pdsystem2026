<?php

namespace App\Console\Commands;

use App\Models\NotaDinas;
use App\Models\NotaDinasParticipant;
use App\Models\User;
use App\Models\Unit;
use App\Models\City;
use Illuminate\Console\Command;

class CreateOverlapTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create-overlap-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test data with overlapping dates for testing overlap warning';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating overlap test data...');

        // Clean up existing overlap test data
        $this->info('Cleaning up existing overlap test data...');
        NotaDinas::where('doc_no', 'like', 'ND/OVERLAP/%')->delete();
        $this->info('Cleanup completed.');

        // Get required data
        $unit = Unit::first();
        $users = User::take(3)->get();
        $city = City::first();

        if (!$unit || $users->count() < 2 || !$city) {
            $this->error('Required data not found. Please ensure you have units, users, and cities.');
            return 1;
        }

        // Create first Nota Dinas (existing)
        $nd1 = NotaDinas::create([
            'doc_no' => 'ND/OVERLAP/001/2025-' . time(),
            'number_is_manual' => true,
            'number_format_id' => null,
            'number_sequence_id' => null,
            'number_scope_unit_id' => $unit->id,
            'to_user_id' => $users[0]->id,
            'from_user_id' => $users[1]->id,
            'tembusan' => 'Tembusan test',
            'nd_date' => now()->subDays(5),
            'sifat' => 'Penting',
            'lampiran_count' => 1,
            'hal' => 'Rapat Koordinasi Test 1',
            'dasar' => 'Dasar test 1',
            'maksud' => 'Maksud test 1',
            'destination_city_id' => $city->id,
            'origin_place_id' => null,
            'start_date' => now()->addDays(10), // 10 days from now
            'end_date' => now()->addDays(12),   // 12 days from now
            'days_count' => 3,
            'requesting_unit_id' => $unit->id,
            'status' => 'DRAFT',
            'created_by' => $users[0]->id,
            'notes' => 'Test data for overlap',
        ]);

        // Add participant to first ND
        NotaDinasParticipant::create([
            'nota_dinas_id' => $nd1->id,
            'user_id' => $users[0]->id,
        ]);

        $this->info("Created first Nota Dinas: {$nd1->doc_no}");

        // Create second Nota Dinas (overlapping dates)
        $nd2 = NotaDinas::create([
            'doc_no' => 'ND/OVERLAP/002/2025-' . time(),
            'number_is_manual' => true,
            'number_format_id' => null,
            'number_sequence_id' => null,
            'number_scope_unit_id' => $unit->id,
            'to_user_id' => $users[1]->id,
            'from_user_id' => $users[0]->id,
            'tembusan' => 'Tembusan test 2',
            'nd_date' => now()->subDays(3),
            'sifat' => 'Penting',
            'lampiran_count' => 1,
            'hal' => 'Meeting Evaluasi Test 2',
            'dasar' => 'Dasar test 2',
            'maksud' => 'Maksud test 2',
            'destination_city_id' => $city->id,
            'origin_place_id' => null,
            'start_date' => now()->addDays(11), // 11 days from now (overlaps with ND1)
            'end_date' => now()->addDays(13),   // 13 days from now (overlaps with ND1)
            'days_count' => 3,
            'requesting_unit_id' => $unit->id,
            'status' => 'DRAFT',
            'created_by' => $users[1]->id,
            'notes' => 'Test data for overlap 2',
        ]);

        // Add same participant to second ND (this will cause overlap)
        NotaDinasParticipant::create([
            'nota_dinas_id' => $nd2->id,
            'user_id' => $users[0]->id, // Same user as ND1
        ]);

        $this->info("Created second Nota Dinas: {$nd2->doc_no}");
        $this->info("User {$users[0]->name} is now in both Nota Dinas with overlapping dates");
        $this->info("ND1: {$nd1->start_date} - {$nd1->end_date}");
        $this->info("ND2: {$nd2->start_date} - {$nd2->end_date}");
        $this->info("Overlap test data created successfully!");

        return 0;
    }
}

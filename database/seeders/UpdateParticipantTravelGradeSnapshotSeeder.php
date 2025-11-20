<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotaDinasParticipant;
use App\Models\User;
use App\Models\TravelGrade;
use Illuminate\Support\Facades\DB;

class UpdateParticipantTravelGradeSnapshotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all participants that don't have travel grade snapshot
        $participants = NotaDinasParticipant::whereNull('user_travel_grade_id_snapshot')->get();
        
        foreach ($participants as $participant) {
            $user = User::find($participant->user_id);
            if ($user && $user->travel_grade_id) {
                $travelGrade = TravelGrade::find($user->travel_grade_id);
                if ($travelGrade) {
                    $participant->update([
                        'user_travel_grade_id_snapshot' => $travelGrade->id,
                        'user_travel_grade_code_snapshot' => $travelGrade->code,
                        'user_travel_grade_name_snapshot' => $travelGrade->name,
                    ]);
                }
            }
        }
        
        $this->command->info('Participant Travel Grade Snapshots updated successfully!');
    }
}

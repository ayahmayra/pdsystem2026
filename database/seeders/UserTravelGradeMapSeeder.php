<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TravelGrade;

class UserTravelGradeMapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get travel grades
        $travelGrades = TravelGrade::all()->keyBy('code');
        
        // Get users
        $users = User::all();
        
        foreach ($users as $user) {
            // Determine travel grade based on rank and position
            $travelGradeCode = $this->determineTravelGrade($user);
            
            if (isset($travelGrades[$travelGradeCode])) {
                $user->update([
                    'travel_grade_id' => $travelGrades[$travelGradeCode]->id,
                ]);
            }
        }
        
        $this->command->info('User Travel Grades updated successfully!');
    }
    
    /**
     * Determine travel grade based on user's rank and position
     */
    private function determineTravelGrade($user)
    {
        // Get rank code from user's rank
        $rankCode = $user->rank->code ?? '';
        $positionDesc = $user->position_desc ?? '';
        
        // Mapping based on rank and position
        if (str_contains($positionDesc, 'Sekretaris Daerah')) {
            return 'BUP'; // Bupati level
        }
        
        if (str_contains($positionDesc, 'Kepala Dinas')) {
            return 'ESELON2_ANGGOTA_DPRD'; // Eselon II
        }
        
        if (str_contains($positionDesc, 'Kepala Bagian')) {
            return 'ESELON3_GOL4'; // Eselon III
        }
        
        // Fallback based on rank
        switch ($rankCode) {
            case 'IV/e':
            case 'IV/d':
                return 'BUP'; // Bupati level
            case 'IV/c':
            case 'IV/b':
                return 'ESELON2_ANGGOTA_DPRD'; // Eselon II
            case 'IV/a':
            case 'III/d':
                return 'ESELON3_GOL4'; // Eselon III
            case 'III/c':
            case 'III/b':
            case 'III/a':
            case 'II/d':
            case 'II/c':
            case 'II/b':
            case 'II/a':
            case 'I/d':
            case 'I/c':
            case 'I/b':
            case 'I/a':
                return 'ESELON4_GOL3_2_1_PPPK_NONASN'; // Staff level
            default:
                return 'ESELON4_GOL3_2_1_PPPK_NONASN'; // Default to staff level
        }
    }
}

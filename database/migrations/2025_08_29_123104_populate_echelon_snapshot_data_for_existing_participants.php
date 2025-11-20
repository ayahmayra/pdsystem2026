<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Populate echelon snapshot data for existing participants
        $participants = DB::table('nota_dinas_participants')->get();
        
        foreach ($participants as $participant) {
            $user = DB::table('users')->where('id', $participant->user_id)->first();
            if ($user) {
                $position = DB::table('positions')->where('id', $user->position_id)->first();
                $echelonId = $position ? $position->echelon_id : null;
                
                DB::table('nota_dinas_participants')
                    ->where('id', $participant->id)
                    ->update(['user_position_echelon_id_snapshot' => $echelonId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear echelon snapshot data
        DB::table('nota_dinas_participants')->update(['user_position_echelon_id_snapshot' => null]);
    }
};

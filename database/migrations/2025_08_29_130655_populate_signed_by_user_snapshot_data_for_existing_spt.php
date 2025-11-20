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
        // Populate signed_by_user snapshot data for existing SPT records
        $spts = DB::table('spt')->get();
        
        foreach ($spts as $spt) {
            if ($spt->signed_by_user_id) {
                $user = DB::table('users')->where('id', $spt->signed_by_user_id)->first();
                if ($user) {
                    $position = DB::table('positions')->where('id', $user->position_id)->first();
                    $unit = DB::table('units')->where('id', $user->unit_id)->first();
                    $rank = DB::table('ranks')->where('id', $user->rank_id)->first();
                    
                    DB::table('spt')
                        ->where('id', $spt->id)
                        ->update([
                            'signed_by_user_name_snapshot' => $user->name,
                            'signed_by_user_gelar_depan_snapshot' => $user->gelar_depan,
                            'signed_by_user_gelar_belakang_snapshot' => $user->gelar_belakang,
                            'signed_by_user_nip_snapshot' => $user->nip,
                            'signed_by_user_unit_id_snapshot' => $user->unit_id,
                            'signed_by_user_unit_name_snapshot' => $unit ? $unit->name : null,
                            'signed_by_user_position_id_snapshot' => $user->position_id,
                            'signed_by_user_position_name_snapshot' => $position ? $position->name : null,
                            'signed_by_user_position_desc_snapshot' => $user->position_desc,
                            'signed_by_user_rank_id_snapshot' => $user->rank_id,
                            'signed_by_user_rank_name_snapshot' => $rank ? $rank->name : null,
                            'signed_by_user_rank_code_snapshot' => $rank ? $rank->code : null,
                            'signed_by_user_position_echelon_id_snapshot' => $position ? $position->echelon_id : null,
                        ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear signed_by_user snapshot data
        DB::table('spt')->update([
            'signed_by_user_name_snapshot' => null,
            'signed_by_user_gelar_depan_snapshot' => null,
            'signed_by_user_gelar_belakang_snapshot' => null,
            'signed_by_user_nip_snapshot' => null,
            'signed_by_user_unit_id_snapshot' => null,
            'signed_by_user_unit_name_snapshot' => null,
            'signed_by_user_position_id_snapshot' => null,
            'signed_by_user_position_name_snapshot' => null,
            'signed_by_user_position_desc_snapshot' => null,
            'signed_by_user_rank_id_snapshot' => null,
            'signed_by_user_rank_name_snapshot' => null,
            'signed_by_user_rank_code_snapshot' => null,
            'signed_by_user_position_echelon_id_snapshot' => null,
        ]);
    }
};

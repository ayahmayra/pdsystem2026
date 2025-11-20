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
        // Populate snapshot data for existing SPPD records
        $sppds = DB::table('sppd')->whereNull('signed_by_user_name_snapshot')->get();
        
        foreach ($sppds as $sppd) {
            if (!$sppd->signed_by_user_id) {
                continue;
            }
            
            $user = DB::table('users')
                ->leftJoin('positions', 'users.position_id', '=', 'positions.id')
                ->leftJoin('units', 'users.unit_id', '=', 'units.id')
                ->leftJoin('ranks', 'users.rank_id', '=', 'ranks.id')
                ->where('users.id', $sppd->signed_by_user_id)
                ->select([
                    'users.name',
                    'users.gelar_depan',
                    'users.gelar_belakang',
                    'users.nip',
                    'users.unit_id',
                    'users.position_id',
                    'users.rank_id',
                    'users.position_desc',
                    'positions.name as position_name',
                    'positions.echelon_id as position_echelon_id',
                    'units.name as unit_name',
                    'ranks.name as rank_name',
                    'ranks.code as rank_code'
                ])
                ->first();
            
            if ($user) {
                DB::table('sppd')->where('id', $sppd->id)->update([
                    'signed_by_user_name_snapshot' => $user->name,
                    'signed_by_user_gelar_depan_snapshot' => $user->gelar_depan,
                    'signed_by_user_gelar_belakang_snapshot' => $user->gelar_belakang,
                    'signed_by_user_nip_snapshot' => $user->nip,
                    'signed_by_user_unit_id_snapshot' => $user->unit_id,
                    'signed_by_user_unit_name_snapshot' => $user->unit_name,
                    'signed_by_user_position_id_snapshot' => $user->position_id,
                    'signed_by_user_position_name_snapshot' => $user->position_name,
                    'signed_by_user_position_desc_snapshot' => $user->position_desc,
                    'signed_by_user_rank_id_snapshot' => $user->rank_id,
                    'signed_by_user_rank_name_snapshot' => $user->rank_name,
                    'signed_by_user_rank_code_snapshot' => $user->rank_code,
                    'signed_by_user_position_echelon_id_snapshot' => $user->position_echelon_id,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear snapshot data
        DB::table('sppd')->update([
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

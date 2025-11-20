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
        // Get all existing participant records
        $participants = DB::table('nota_dinas_participants')->get();

        foreach ($participants as $participant) {
            if ($participant->user_id) {
                $user = DB::table('users')
                    ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
                    ->leftJoin('units', 'units.id', '=', 'users.unit_id')
                    ->leftJoin('ranks', 'ranks.id', '=', 'users.rank_id')
                    ->where('users.id', $participant->user_id)
                    ->select(
                        'users.name',
                        'users.gelar_depan',
                        'users.gelar_belakang',
                        'users.nip',
                        'users.unit_id',
                        'units.name as unit_name',
                        'users.position_id',
                        'positions.name as position_name',
                        'users.position_desc',
                        'users.rank_id',
                        'ranks.name as rank_name',
                        'ranks.code as rank_code'
                    )
                    ->first();

                if ($user) {
                    DB::table('nota_dinas_participants')
                        ->where('id', $participant->id)
                        ->update([
                            'user_name_snapshot' => $user->name,
                            'user_gelar_depan_snapshot' => $user->gelar_depan,
                            'user_gelar_belakang_snapshot' => $user->gelar_belakang,
                            'user_nip_snapshot' => $user->nip,
                            'user_unit_id_snapshot' => $user->unit_id,
                            'user_unit_name_snapshot' => $user->unit_name,
                            'user_position_id_snapshot' => $user->position_id,
                            'user_position_name_snapshot' => $user->position_name,
                            'user_position_desc_snapshot' => $user->position_desc,
                            'user_rank_id_snapshot' => $user->rank_id,
                            'user_rank_name_snapshot' => $user->rank_name,
                            'user_rank_code_snapshot' => $user->rank_code,
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
        // Clear all snapshot data
        DB::table('nota_dinas_participants')->update([
            'user_name_snapshot' => null,
            'user_gelar_depan_snapshot' => null,
            'user_gelar_belakang_snapshot' => null,
            'user_nip_snapshot' => null,
            'user_unit_id_snapshot' => null,
            'user_unit_name_snapshot' => null,
            'user_position_id_snapshot' => null,
            'user_position_name_snapshot' => null,
            'user_position_desc_snapshot' => null,
            'user_rank_id_snapshot' => null,
            'user_rank_name_snapshot' => null,
            'user_rank_code_snapshot' => null,
        ]);
    }
};

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
        // Get all existing Nota Dinas records
        $notaDinas = DB::table('nota_dinas')->get();

        foreach ($notaDinas as $nd) {
            $updates = [];

            // Get from_user data
            if ($nd->from_user_id) {
                $fromUser = DB::table('users')
                    ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
                    ->leftJoin('units', 'units.id', '=', 'users.unit_id')
                    ->leftJoin('ranks', 'ranks.id', '=', 'users.rank_id')
                    ->where('users.id', $nd->from_user_id)
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

                if ($fromUser) {
                    $updates = array_merge($updates, [
                        'from_user_name_snapshot' => $fromUser->name,
                        'from_user_gelar_depan_snapshot' => $fromUser->gelar_depan,
                        'from_user_gelar_belakang_snapshot' => $fromUser->gelar_belakang,
                        'from_user_nip_snapshot' => $fromUser->nip,
                        'from_user_unit_id_snapshot' => $fromUser->unit_id,
                        'from_user_unit_name_snapshot' => $fromUser->unit_name,
                        'from_user_position_id_snapshot' => $fromUser->position_id,
                        'from_user_position_name_snapshot' => $fromUser->position_name,
                        'from_user_position_desc_snapshot' => $fromUser->position_desc,
                        'from_user_rank_id_snapshot' => $fromUser->rank_id,
                        'from_user_rank_name_snapshot' => $fromUser->rank_name,
                        'from_user_rank_code_snapshot' => $fromUser->rank_code,
                    ]);
                }
            }

            // Get to_user data
            if ($nd->to_user_id) {
                $toUser = DB::table('users')
                    ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
                    ->leftJoin('units', 'units.id', '=', 'users.unit_id')
                    ->leftJoin('ranks', 'ranks.id', '=', 'users.rank_id')
                    ->where('users.id', $nd->to_user_id)
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

                if ($toUser) {
                    $updates = array_merge($updates, [
                        'to_user_name_snapshot' => $toUser->name,
                        'to_user_gelar_depan_snapshot' => $toUser->gelar_depan,
                        'to_user_gelar_belakang_snapshot' => $toUser->gelar_belakang,
                        'to_user_nip_snapshot' => $toUser->nip,
                        'to_user_unit_id_snapshot' => $toUser->unit_id,
                        'to_user_unit_name_snapshot' => $toUser->unit_name,
                        'to_user_position_id_snapshot' => $toUser->position_id,
                        'to_user_position_name_snapshot' => $toUser->position_name,
                        'to_user_position_desc_snapshot' => $toUser->position_desc,
                        'to_user_rank_id_snapshot' => $toUser->rank_id,
                        'to_user_rank_name_snapshot' => $toUser->rank_name,
                        'to_user_rank_code_snapshot' => $toUser->rank_code,
                    ]);
                }
            }

            // Update the record if we have data to update
            if (!empty($updates)) {
                DB::table('nota_dinas')
                    ->where('id', $nd->id)
                    ->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear all snapshot data
        DB::table('nota_dinas')->update([
            'from_user_name_snapshot' => null,
            'from_user_gelar_depan_snapshot' => null,
            'from_user_gelar_belakang_snapshot' => null,
            'from_user_nip_snapshot' => null,
            'from_user_unit_id_snapshot' => null,
            'from_user_unit_name_snapshot' => null,
            'from_user_position_id_snapshot' => null,
            'from_user_position_name_snapshot' => null,
            'from_user_position_desc_snapshot' => null,
            'from_user_rank_id_snapshot' => null,
            'from_user_rank_name_snapshot' => null,
            'from_user_rank_code_snapshot' => null,
            'to_user_name_snapshot' => null,
            'to_user_gelar_depan_snapshot' => null,
            'to_user_gelar_belakang_snapshot' => null,
            'to_user_nip_snapshot' => null,
            'to_user_unit_id_snapshot' => null,
            'to_user_unit_name_snapshot' => null,
            'to_user_position_id_snapshot' => null,
            'to_user_position_name_snapshot' => null,
            'to_user_position_desc_snapshot' => null,
            'to_user_rank_id_snapshot' => null,
            'to_user_rank_name_snapshot' => null,
            'to_user_rank_code_snapshot' => null,
        ]);
    }
};

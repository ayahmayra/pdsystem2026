<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaDinasParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'nota_dinas_id', 'user_id', 'role_in_trip',
        // Snapshot fields for user data
        'user_name_snapshot', 'user_gelar_depan_snapshot', 'user_gelar_belakang_snapshot',
        'user_nip_snapshot', 'user_unit_id_snapshot', 'user_unit_name_snapshot',
        'user_position_id_snapshot', 'user_position_name_snapshot', 'user_position_desc_snapshot',
        'user_rank_id_snapshot', 'user_rank_name_snapshot', 'user_rank_code_snapshot',
        'user_position_echelon_id_snapshot',
        // Snapshot fields for travel grade
        'user_travel_grade_id_snapshot', 'user_travel_grade_code_snapshot', 'user_travel_grade_name_snapshot',
    ];

    public function notaDinas() { return $this->belongsTo(NotaDinas::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function travelGradeSnapshot() { return $this->belongsTo(TravelGrade::class, 'user_travel_grade_id_snapshot'); }

    /**
     * Get snapshot data for user
     */
    public function getUserSnapshotAttribute()
    {
        return [
            'name' => $this->user_name_snapshot ?: $this->user?->name,
            'gelar_depan' => $this->user_gelar_depan_snapshot ?: $this->user?->gelar_depan,
            'gelar_belakang' => $this->user_gelar_belakang_snapshot ?: $this->user?->gelar_belakang,
            'nip' => $this->user_nip_snapshot ?: $this->user?->nip,
            'unit_id' => $this->user_unit_id_snapshot ?: $this->user?->unit_id,
            'unit_name' => $this->user_unit_name_snapshot ?: $this->user?->unit?->name,
            'position_id' => $this->user_position_id_snapshot ?: $this->user?->position_id,
            'position_name' => $this->user_position_name_snapshot ?: $this->user?->position?->name,
            'position_desc' => $this->user_position_desc_snapshot ?: $this->user?->position_desc,
            'rank_id' => $this->user_rank_id_snapshot ?: $this->user?->rank_id,
            'rank_name' => $this->user_rank_name_snapshot ?: $this->user?->rank?->name,
            'rank_code' => $this->user_rank_code_snapshot ?: $this->user?->rank?->code,
            'position_echelon_id' => $this->user_position_echelon_id_snapshot ?: $this->user?->position?->echelon?->id,
            'travel_grade_id' => $this->user_travel_grade_id_snapshot ?: $this->user?->travel_grade_id,
            'travel_grade_code' => $this->user_travel_grade_code_snapshot ?: $this->user?->travelGrade?->code,
            'travel_grade_name' => $this->user_travel_grade_name_snapshot ?: $this->user?->travelGrade?->name,
        ];
    }

    /**
     * Create snapshot of user data
     */
    public function createUserSnapshot()
    {
        if ($this->user) {
            $this->update([
                'user_name_snapshot' => $this->user->name,
                'user_gelar_depan_snapshot' => $this->user->gelar_depan,
                'user_gelar_belakang_snapshot' => $this->user->gelar_belakang,
                'user_nip_snapshot' => $this->user->nip,
                'user_unit_id_snapshot' => $this->user->unit_id,
                'user_unit_name_snapshot' => $this->user->unit?->name,
                'user_position_id_snapshot' => $this->user->position_id,
                'user_position_name_snapshot' => $this->user->position?->name,
                'user_position_desc_snapshot' => $this->user->position_desc,
                'user_rank_id_snapshot' => $this->user->rank_id,
                'user_rank_name_snapshot' => $this->user->rank?->name,
                'user_rank_code_snapshot' => $this->user->rank?->code,
                'user_position_echelon_id_snapshot' => $this->user->position?->echelon?->id,
                'user_travel_grade_id_snapshot' => $this->user->travel_grade_id,
                'user_travel_grade_code_snapshot' => $this->user->travelGrade?->code,
                'user_travel_grade_name_snapshot' => $this->user->travelGrade?->name,
            ]);
        }
    }
}

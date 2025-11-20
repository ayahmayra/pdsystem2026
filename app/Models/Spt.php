<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Spt extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'spt';

    protected $fillable = [
        'doc_no', 'number_is_manual', 'number_manual_reason', 'number_format_id', 'number_sequence_id', 'number_scope_unit_id',
        'nota_dinas_id', 'spt_date', 'signed_by_user_id', 'assignment_title',
        'origin_place_id', 'destination_city_id', 'start_date', 'end_date', 'days_count', 'funding_source', 'status',
        'notes',
        // Snapshot fields for signed_by_user data
        'signed_by_user_name_snapshot', 'signed_by_user_gelar_depan_snapshot', 'signed_by_user_gelar_belakang_snapshot',
        'signed_by_user_nip_snapshot', 'signed_by_user_unit_id_snapshot', 'signed_by_user_unit_name_snapshot',
        'signed_by_user_position_id_snapshot', 'signed_by_user_position_name_snapshot', 'signed_by_user_position_desc_snapshot',
        'signed_by_user_rank_id_snapshot', 'signed_by_user_rank_name_snapshot', 'signed_by_user_rank_code_snapshot',
        'signed_by_user_position_echelon_id_snapshot',
    ];

    public function notaDinas() { return $this->belongsTo(NotaDinas::class); }
    public function signedByUser() { return $this->belongsTo(User::class, 'signed_by_user_id'); }
    public function originPlace() { return $this->belongsTo(OrgPlace::class, 'origin_place_id'); }
    public function destinationCity() { return $this->belongsTo(City::class, 'destination_city_id'); }
    public function members() { return $this->hasMany(SptMember::class); }
    public function tripReport() { return $this->hasOne(TripReport::class); }
    public function sppds() { return $this->hasMany(Sppd::class); }
    
    // Helper method untuk mendapatkan peserta dari Nota Dinas
    public function getParticipants()
    {
        return $this->notaDinas?->participants ?? collect();
    }

    // Accessor untuk place_from (menggunakan origin_place)
    public function getPlaceFromAttribute()
    {
        return $this->originPlace?->name ?? '';
    }

    // Accessor untuk place_to (menggunakan destination_city)
    public function getPlaceToAttribute()
    {
        return $this->destinationCity?->name ?? '';
    }

    // Accessor untuk depart_date (menggunakan start_date)
    public function getDepartDateAttribute()
    {
        return $this->start_date;
    }

    // Accessor untuk return_date (menggunakan end_date)
    public function getReturnDateAttribute()
    {
        return $this->end_date;
    }

    /**
     * Get snapshot data for participants from Nota Dinas
     */
    public function getParticipantsSnapshot()
    {
        if (!$this->notaDinas) {
            return collect();
        }

        return $this->notaDinas->participants->map(function ($participant) {
            return [
                'name' => $participant->user_name_snapshot ?: $participant->user?->name,
                'gelar_depan' => $participant->user_gelar_depan_snapshot ?: $participant->user?->gelar_depan,
                'gelar_belakang' => $participant->user_gelar_belakang_snapshot ?: $participant->user?->gelar_belakang,
                'nip' => $participant->user_nip_snapshot ?: $participant->user?->nip,
                'unit_id' => $participant->user_unit_id_snapshot ?: $participant->user?->unit_id,
                'unit_name' => $participant->user_unit_name_snapshot ?: $participant->user?->unit?->name,
                'position_id' => $participant->user_position_id_snapshot ?: $participant->user?->position_id,
                'position_name' => $participant->user_position_name_snapshot ?: $participant->user?->position?->name,
                'position_desc' => $participant->user_position_desc_snapshot ?: $participant->user?->position_desc,
                'rank_id' => $participant->user_rank_id_snapshot ?: $participant->user?->rank_id,
                'rank_name' => $participant->user_rank_name_snapshot ?: $participant->user?->rank?->name,
                'rank_code' => $participant->user_rank_code_snapshot ?: $participant->user?->rank?->code,
                'position_echelon_id' => $participant->user_position_echelon_id_snapshot ?: $participant->user?->position?->echelon?->id,
            ];
        });
    }

    /**
     * Get sorted participants snapshot data from Nota Dinas
     */
    public function getSortedParticipantsSnapshot()
    {
        if (!$this->notaDinas) {
            return collect();
        }

        return $this->notaDinas->getSortedParticipants()->map(function ($participant) {
            return [
                'name' => $participant->user_name_snapshot ?: $participant->user?->name,
                'gelar_depan' => $participant->user_gelar_depan_snapshot ?: $participant->user?->gelar_depan,
                'gelar_belakang' => $participant->user_gelar_belakang_snapshot ?: $participant->user?->gelar_belakang,
                'nip' => $participant->user_nip_snapshot ?: $participant->user?->nip,
                'unit_id' => $participant->user_unit_id_snapshot ?: $participant->user?->unit_id,
                'unit_name' => $participant->user_unit_name_snapshot ?: $participant->user?->unit?->name,
                'position_id' => $participant->user_position_id_snapshot ?: $participant->user?->position_id,
                'position_name' => $participant->user_position_name_snapshot ?: $participant->user?->position?->name,
                'position_desc' => $participant->user_position_desc_snapshot ?: $participant->user?->position_desc,
                'rank_id' => $participant->user_rank_id_snapshot ?: $participant->user?->rank_id,
                'rank_name' => $participant->user_rank_name_snapshot ?: $participant->user?->rank?->name,
                'rank_code' => $participant->user_rank_code_snapshot ?: $participant->user?->rank?->code,
                'position_echelon_id' => $participant->user_position_echelon_id_snapshot ?: $participant->user?->position?->echelon?->id,
            ];
        });
    }

    /**
     * Get snapshot data for signed_by_user (penandatangan SPT)
     */
    public function getSignedByUserSnapshot()
    {
        return [
            'name' => $this->signed_by_user_name_snapshot ?: $this->signedByUser?->name,
            'gelar_depan' => $this->signed_by_user_gelar_depan_snapshot ?: $this->signedByUser?->gelar_depan,
            'gelar_belakang' => $this->signed_by_user_gelar_belakang_snapshot ?: $this->signedByUser?->gelar_belakang,
            'nip' => $this->signed_by_user_nip_snapshot ?: $this->signedByUser?->nip,
            'unit_id' => $this->signed_by_user_unit_id_snapshot ?: $this->signedByUser?->unit_id,
            'unit_name' => $this->signed_by_user_unit_name_snapshot ?: $this->signedByUser?->unit?->name,
            'position_id' => $this->signed_by_user_position_id_snapshot ?: $this->signedByUser?->position_id,
            'position_name' => $this->signed_by_user_position_name_snapshot ?: $this->signedByUser?->position?->name,
            'position_desc' => $this->signed_by_user_position_desc_snapshot ?: $this->signedByUser?->position_desc,
            'rank_id' => $this->signed_by_user_rank_id_snapshot ?: $this->signedByUser?->rank_id,
            'rank_name' => $this->signed_by_user_rank_name_snapshot ?: $this->signedByUser?->rank?->name,
            'rank_code' => $this->signed_by_user_rank_code_snapshot ?: $this->signedByUser?->rank?->code,
            'position_echelon_id' => $this->signed_by_user_position_echelon_id_snapshot ?: $this->signedByUser?->position?->echelon?->id,
        ];
    }

    /**
     * Create snapshot of signed_by_user data
     */
    public function createSignedByUserSnapshot()
    {
        if ($this->signedByUser) {
            $this->update([
                'signed_by_user_name_snapshot' => $this->signedByUser->name,
                'signed_by_user_gelar_depan_snapshot' => $this->signedByUser->gelar_depan,
                'signed_by_user_gelar_belakang_snapshot' => $this->signedByUser->gelar_belakang,
                'signed_by_user_nip_snapshot' => $this->signedByUser->nip,
                'signed_by_user_unit_id_snapshot' => $this->signedByUser->unit_id,
                'signed_by_user_unit_name_snapshot' => $this->signedByUser->unit?->name,
                'signed_by_user_position_id_snapshot' => $this->signedByUser->position_id,
                'signed_by_user_position_name_snapshot' => $this->signedByUser->position?->name,
                'signed_by_user_position_desc_snapshot' => $this->signedByUser->position_desc,
                'signed_by_user_rank_id_snapshot' => $this->signedByUser->rank_id,
                'signed_by_user_rank_name_snapshot' => $this->signedByUser->rank?->name,
                'signed_by_user_rank_code_snapshot' => $this->signedByUser->rank?->code,
                'signed_by_user_position_echelon_id_snapshot' => $this->signedByUser->position?->echelon?->id,
            ]);
        }
    }

    /**
     * Get snapshot data for from_user (penandatangan) from Nota Dinas
     */
    public function getFromUserSnapshot()
    {
        if (!$this->notaDinas) {
            return null;
        }

        return [
            'name' => $this->notaDinas->from_user_name_snapshot ?: $this->notaDinas->fromUser?->name,
            'gelar_depan' => $this->notaDinas->from_user_gelar_depan_snapshot ?: $this->notaDinas->fromUser?->gelar_depan,
            'gelar_belakang' => $this->notaDinas->from_user_gelar_belakang_snapshot ?: $this->notaDinas->fromUser?->gelar_belakang,
            'nip' => $this->notaDinas->from_user_nip_snapshot ?: $this->notaDinas->fromUser?->nip,
            'unit_id' => $this->notaDinas->from_user_unit_id_snapshot ?: $this->notaDinas->fromUser?->unit_id,
            'unit_name' => $this->notaDinas->from_user_unit_name_snapshot ?: $this->notaDinas->fromUser?->unit?->name,
            'position_id' => $this->notaDinas->from_user_position_id_snapshot ?: $this->notaDinas->fromUser?->position_id,
            'position_name' => $this->notaDinas->from_user_position_name_snapshot ?: $this->notaDinas->fromUser?->position?->name,
            'position_desc' => $this->notaDinas->from_user_position_desc_snapshot ?: $this->notaDinas->fromUser?->position_desc,
            'rank_id' => $this->notaDinas->from_user_rank_id_snapshot ?: $this->notaDinas->fromUser?->rank_id,
            'rank_name' => $this->notaDinas->from_user_rank_name_snapshot ?: $this->notaDinas->fromUser?->rank?->name,
            'rank_code' => $this->notaDinas->from_user_rank_code_snapshot ?: $this->notaDinas->fromUser?->rank?->code,
        ];
    }

    /**
     * Get snapshot data for to_user (tujuan) from Nota Dinas
     */
    public function getToUserSnapshot()
    {
        if (!$this->notaDinas) {
            return null;
        }

        return [
            'name' => $this->notaDinas->to_user_name_snapshot ?: $this->notaDinas->toUser?->name,
            'gelar_depan' => $this->notaDinas->to_user_gelar_depan_snapshot ?: $this->notaDinas->toUser?->gelar_depan,
            'gelar_belakang' => $this->notaDinas->to_user_gelar_belakang_snapshot ?: $this->notaDinas->toUser?->gelar_belakang,
            'nip' => $this->notaDinas->to_user_nip_snapshot ?: $this->notaDinas->toUser?->nip,
            'unit_id' => $this->notaDinas->to_user_unit_id_snapshot ?: $this->notaDinas->toUser?->unit_id,
            'unit_name' => $this->notaDinas->to_user_unit_name_snapshot ?: $this->notaDinas->toUser?->unit?->name,
            'position_id' => $this->notaDinas->to_user_position_id_snapshot ?: $this->notaDinas->toUser?->position_id,
            'position_name' => $this->notaDinas->to_user_position_name_snapshot ?: $this->notaDinas->toUser?->position?->name,
            'position_desc' => $this->notaDinas->to_user_position_desc_snapshot ?: $this->notaDinas->toUser?->position_desc,
            'rank_id' => $this->notaDinas->to_user_rank_id_snapshot ?: $this->notaDinas->toUser?->rank_id,
            'rank_name' => $this->notaDinas->to_user_rank_name_snapshot ?: $this->notaDinas->toUser?->rank?->name,
            'rank_code' => $this->notaDinas->to_user_rank_code_snapshot ?: $this->notaDinas->toUser?->rank?->code,
        ];
    }

    /**
     * Get travel grade snapshot from Nota Dinas
     */
    public function getTravelGradeSnapshot()
    {
        if (!$this->notaDinas) {
            return null;
        }

        // Get travel grade from the first participant after sorting
        $sortedParticipants = $this->notaDinas->getSortedParticipants();
        $firstParticipant = $sortedParticipants->first();
        if ($firstParticipant) {
            return [
                'id' => $firstParticipant->user_travel_grade_id_snapshot,
                'code' => $firstParticipant->user_travel_grade_code_snapshot,
                'name' => $firstParticipant->user_travel_grade_name_snapshot,
            ];
        }

        return null;
    }
}

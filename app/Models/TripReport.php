<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TripReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'doc_no', 'number_is_manual', 'number_manual_reason', 'number_format_id', 'number_sequence_id',
        'number_scope_unit_id', 'spt_id', 'report_no', 'report_date', 'place_from', 'place_to',
        'depart_date', 'return_date', 'activities', 'created_by_user_id',
    ];

    public function spt() { return $this->belongsTo(Spt::class); }
    public function createdByUser() { return $this->belongsTo(User::class, 'created_by_user_id'); }
    public function signers() { return $this->hasMany(TripReportSigner::class); }
    // Supporting documents now relate to Nota Dinas, not Trip Report
    // public function supportingDocuments() { return $this->hasMany(SupportingDocument::class); }

    /**
     * Get snapshot data for participants from Nota Dinas
     */
    public function getParticipantsSnapshot()
    {
        if (!$this->spt?->notaDinas) {
            return collect();
        }

        return $this->spt->notaDinas->participants->map(function ($participant) {
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
            ];
        });
    }

    /**
     * Get snapshot data for created_by_user from Nota Dinas
     */
    public function getCreatedByUserSnapshot()
    {
        if (!$this->spt?->notaDinas) {
            return null;
        }

        return [
            'name' => $this->spt->notaDinas->from_user_name_snapshot ?: $this->spt->notaDinas->fromUser?->name,
            'gelar_depan' => $this->spt->notaDinas->from_user_gelar_depan_snapshot ?: $this->spt->notaDinas->fromUser?->gelar_depan,
            'gelar_belakang' => $this->spt->notaDinas->from_user_gelar_belakang_snapshot ?: $this->spt->notaDinas->fromUser?->gelar_belakang,
            'nip' => $this->spt->notaDinas->from_user_nip_snapshot ?: $this->spt->notaDinas->fromUser?->nip,
            'unit_id' => $this->spt->notaDinas->from_user_unit_id_snapshot ?: $this->spt->notaDinas->fromUser?->unit_id,
            'unit_name' => $this->spt->notaDinas->from_user_unit_name_snapshot ?: $this->spt->notaDinas->fromUser?->unit?->name,
            'position_id' => $this->spt->notaDinas->from_user_position_id_snapshot ?: $this->spt->notaDinas->fromUser?->position_id,
            'position_name' => $this->spt->notaDinas->from_user_position_name_snapshot ?: $this->spt->notaDinas->fromUser?->position?->name,
            'position_desc' => $this->spt->notaDinas->from_user_position_desc_snapshot ?: $this->spt->notaDinas->fromUser?->position_desc,
            'rank_id' => $this->spt->notaDinas->from_user_rank_id_snapshot ?: $this->spt->notaDinas->fromUser?->rank_id,
            'rank_name' => $this->spt->notaDinas->from_user_rank_name_snapshot ?: $this->spt->notaDinas->fromUser?->rank?->name,
            'rank_code' => $this->spt->notaDinas->from_user_rank_code_snapshot ?: $this->spt->notaDinas->fromUser?->rank?->code,
        ];
    }
}

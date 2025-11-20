<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotaDinas extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'doc_no', 'number_is_manual', 'number_manual_reason', 'number_format_id', 'number_sequence_id',
        'number_scope_unit_id', 'to_user_id', 'from_user_id', 'tembusan', 'nd_date',
        'sifat', 'lampiran_count', 'hal', 'custom_signer_title', 'dasar', 'maksud', 'destination_city_id', 'origin_place_id', 'start_date',
        'end_date', 'trip_type', 'requesting_unit_id', 'status', 'created_by',
        'approved_by', 'approved_at', 'notes',
        // Snapshot fields for from_user
        'from_user_name_snapshot', 'from_user_gelar_depan_snapshot', 'from_user_gelar_belakang_snapshot',
        'from_user_nip_snapshot', 'from_user_unit_id_snapshot', 'from_user_unit_name_snapshot',
        'from_user_position_id_snapshot', 'from_user_position_name_snapshot', 'from_user_position_desc_snapshot',
        'from_user_rank_id_snapshot', 'from_user_rank_name_snapshot', 'from_user_rank_code_snapshot',
        // Snapshot fields for to_user
        'to_user_name_snapshot', 'to_user_gelar_depan_snapshot', 'to_user_gelar_belakang_snapshot',
        'to_user_nip_snapshot', 'to_user_unit_id_snapshot', 'to_user_unit_name_snapshot',
        'to_user_position_id_snapshot', 'to_user_position_name_snapshot', 'to_user_position_desc_snapshot',
        'to_user_rank_id_snapshot', 'to_user_rank_name_snapshot', 'to_user_rank_code_snapshot',
    ];

    // Relasi
    public function participants() { return $this->hasMany(NotaDinasParticipant::class); }
    public function toUser() { return $this->belongsTo(User::class, 'to_user_id'); }
    public function fromUser() { return $this->belongsTo(User::class, 'from_user_id'); }
    public function destinationCity() { return $this->belongsTo(City::class, 'destination_city_id'); }
    public function originPlace() { return $this->belongsTo(OrgPlace::class, 'origin_place_id'); }
    public function requestingUnit() { return $this->belongsTo(Unit::class, 'requesting_unit_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
    public function numberFormat() { return $this->belongsTo(DocNumberFormat::class, 'number_format_id'); }
    public function numberSequence() { return $this->belongsTo(NumberSequence::class, 'number_sequence_id'); }
    public function numberScopeUnit() { return $this->belongsTo(Unit::class, 'number_scope_unit_id'); }
    public function spt() { return $this->hasOne(Spt::class, 'nota_dinas_id'); }
    public function supportingDocuments() { return $this->hasMany(SupportingDocument::class); }

    /**
     * Get snapshot data for from_user (penandatangan)
     */
    public function getFromUserSnapshotAttribute()
    {
        return [
            'name' => $this->from_user_name_snapshot ?: $this->fromUser?->name,
            'gelar_depan' => $this->from_user_gelar_depan_snapshot ?: $this->fromUser?->gelar_depan,
            'gelar_belakang' => $this->from_user_gelar_belakang_snapshot ?: $this->fromUser?->gelar_belakang,
            'nip' => $this->from_user_nip_snapshot ?: $this->fromUser?->nip,
            'unit_id' => $this->from_user_unit_id_snapshot ?: $this->fromUser?->unit_id,
            'unit_name' => $this->from_user_unit_name_snapshot ?: $this->fromUser?->unit?->name,
            'position_id' => $this->from_user_position_id_snapshot ?: $this->fromUser?->position_id,
            'position_name' => $this->from_user_position_name_snapshot ?: $this->fromUser?->position?->name,
            'position_desc' => $this->from_user_position_desc_snapshot ?: $this->fromUser?->position_desc,
            'rank_id' => $this->from_user_rank_id_snapshot ?: $this->fromUser?->rank_id,
            'rank_name' => $this->from_user_rank_name_snapshot ?: $this->fromUser?->rank?->name,
            'rank_code' => $this->from_user_rank_code_snapshot ?: $this->fromUser?->rank?->code,
        ];
    }

    /**
     * Get snapshot data for to_user (tujuan)
     */
    public function getToUserSnapshotAttribute()
    {
        return [
            'name' => $this->to_user_name_snapshot ?: $this->toUser?->name,
            'gelar_depan' => $this->to_user_gelar_depan_snapshot ?: $this->toUser?->gelar_depan,
            'gelar_belakang' => $this->to_user_gelar_belakang_snapshot ?: $this->toUser?->gelar_belakang,
            'nip' => $this->to_user_nip_snapshot ?: $this->toUser?->nip,
            'unit_id' => $this->to_user_unit_id_snapshot ?: $this->toUser?->unit_id,
            'unit_name' => $this->to_user_unit_name_snapshot ?: $this->toUser?->unit?->name,
            'position_id' => $this->to_user_position_id_snapshot ?: $this->toUser?->position_id,
            'position_name' => $this->to_user_position_name_snapshot ?: $this->toUser?->position?->name,
            'position_desc' => $this->to_user_position_desc_snapshot ?: $this->toUser?->position_desc,
            'rank_id' => $this->to_user_rank_id_snapshot ?: $this->toUser?->rank_id,
            'rank_name' => $this->to_user_rank_name_snapshot ?: $this->toUser?->rank?->name,
            'rank_code' => $this->to_user_rank_code_snapshot ?: $this->toUser?->rank?->code,
        ];
    }

    /**
     * Get participants sorted by eselon, rank, and NIP
     */
    public function getSortedParticipants()
    {
        return $this->participants->sort(function ($a, $b) {
            // 1. Sort by eselon (position_echelon_id) - lower number = higher eselon
            $ea = $a->user_position_echelon_id_snapshot ?? $a->user?->position?->echelon?->id ?? 999999;
            $eb = $b->user_position_echelon_id_snapshot ?? $b->user?->position?->echelon?->id ?? 999999;
            if ($ea !== $eb) return $ea <=> $eb;
            
            // 2. Sort by rank (rank_id) - higher number = higher rank
            $ra = $a->user_rank_id_snapshot ?? $a->user?->rank?->id ?? 0;
            $rb = $b->user_rank_id_snapshot ?? $b->user?->rank?->id ?? 0;
            if ($ra !== $rb) return $rb <=> $ra; // DESC order for rank
            
            // 3. Sort by NIP (alphabetical)
            $na = (string)($a->user_nip_snapshot ?? $a->user?->nip ?? '');
            $nb = (string)($b->user_nip_snapshot ?? $b->user?->nip ?? '');
            return strcmp($na, $nb);
        })->values();
    }

    /**
     * Create snapshot of user data
     */
    public function createUserSnapshot()
    {
        // Snapshot from_user data
        if ($this->fromUser) {
            $this->update([
                'from_user_name_snapshot' => $this->fromUser->name,
                'from_user_gelar_depan_snapshot' => $this->fromUser->gelar_depan,
                'from_user_gelar_belakang_snapshot' => $this->fromUser->gelar_belakang,
                'from_user_nip_snapshot' => $this->fromUser->nip,
                'from_user_unit_id_snapshot' => $this->fromUser->unit_id,
                'from_user_unit_name_snapshot' => $this->fromUser->unit?->name,
                'from_user_position_id_snapshot' => $this->fromUser->position_id,
                'from_user_position_name_snapshot' => $this->fromUser->position?->name,
                'from_user_position_desc_snapshot' => $this->fromUser->position_desc,
                'from_user_rank_id_snapshot' => $this->fromUser->rank_id,
                'from_user_rank_name_snapshot' => $this->fromUser->rank?->name,
                'from_user_rank_code_snapshot' => $this->fromUser->rank?->code,
            ]);
        }

        // Snapshot to_user data
        if ($this->toUser) {
            $this->update([
                'to_user_name_snapshot' => $this->toUser->name,
                'to_user_gelar_depan_snapshot' => $this->toUser->gelar_depan,
                'to_user_gelar_belakang_snapshot' => $this->toUser->gelar_belakang,
                'to_user_nip_snapshot' => $this->toUser->nip,
                'to_user_unit_id_snapshot' => $this->toUser->unit_id,
                'to_user_unit_name_snapshot' => $this->toUser->unit?->name,
                'to_user_position_id_snapshot' => $this->toUser->position_id,
                'to_user_position_name_snapshot' => $this->toUser->position?->name,
                'to_user_position_desc_snapshot' => $this->toUser->position_desc,
                'to_user_rank_id_snapshot' => $this->toUser->rank_id,
                'to_user_rank_name_snapshot' => $this->toUser->rank?->name,
                'to_user_rank_code_snapshot' => $this->toUser->rank?->code,
            ]);
        }
    }
}

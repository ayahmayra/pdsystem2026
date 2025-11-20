<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'doc_no', 'number_is_manual', 'number_manual_reason', 'number_format_id', 'number_sequence_id',
        'number_scope_unit_id', 'sppd_id', 'travel_grade_id', 'receipt_no', 'receipt_date', 'payee_user_id',
        'rekening_belanja_id', 'account_code', 'treasurer_user_id', 'treasurer_user_name_snapshot',
        'treasurer_user_gelar_depan_snapshot', 'treasurer_user_gelar_belakang_snapshot', 'treasurer_user_nip_snapshot',
        'treasurer_user_unit_id_snapshot', 'treasurer_user_unit_name_snapshot', 'treasurer_user_position_id_snapshot',
        'treasurer_user_position_name_snapshot', 'treasurer_user_position_desc_snapshot', 'treasurer_user_rank_id_snapshot',
        'treasurer_user_rank_name_snapshot', 'treasurer_user_rank_code_snapshot', 'treasurer_title',
        'total_amount', 'notes', 'status',
    ];

    public function sppd() { return $this->belongsTo(Sppd::class); }
    public function travelGrade() { return $this->belongsTo(TravelGrade::class, 'travel_grade_id'); }
    public function payeeUser() { return $this->belongsTo(User::class, 'payee_user_id'); }
    public function treasurerUser() { return $this->belongsTo(User::class, 'treasurer_user_id'); }
    public function rekeningBelanja() { return $this->belongsTo(RekeningBelanja::class, 'rekening_belanja_id'); }
    public function lines() { return $this->hasMany(ReceiptLine::class); }

    /**
     * Get snapshot data for payee_user from Nota Dinas
     */
    public function getPayeeUserSnapshot()
    {
        if (!$this->sppd?->spt?->notaDinas) {
            return null;
        }

        // Find the participant in Nota Dinas that matches this receipt payee
        $participant = $this->sppd->spt->notaDinas->participants
            ->where('user_id', $this->payee_user_id)
            ->first();

        if ($participant) {
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
        }

        return null;
    }



    /**
     * Get snapshot data for treasurer_user (Bendahara)
     */
    public function getTreasurerUserSnapshot()
    {
        return [
            'name' => $this->treasurer_user_name_snapshot ?: $this->treasurerUser?->name,
            'gelar_depan' => $this->treasurer_user_gelar_depan_snapshot ?: $this->treasurerUser?->gelar_depan,
            'gelar_belakang' => $this->treasurer_user_gelar_belakang_snapshot ?: $this->treasurerUser?->gelar_belakang,
            'nip' => $this->treasurer_user_nip_snapshot ?: $this->treasurerUser?->nip,
            'unit_id' => $this->treasurer_user_unit_id_snapshot ?: $this->treasurerUser?->unit_id,
            'unit_name' => $this->treasurer_user_unit_name_snapshot ?: $this->treasurerUser?->unit?->name,
            'position_id' => $this->treasurer_user_position_id_snapshot ?: $this->treasurerUser?->position_id,
            'position_name' => $this->treasurer_user_position_name_snapshot ?: $this->treasurerUser?->position?->name,
            'position_desc' => $this->treasurer_user_position_desc_snapshot ?: $this->treasurerUser?->position_desc,
            'rank_id' => $this->treasurer_user_rank_id_snapshot ?: $this->treasurerUser?->rank_id,
            'rank_name' => $this->treasurer_user_rank_name_snapshot ?: $this->treasurerUser?->rank?->name,
            'rank_code' => $this->treasurer_user_rank_code_snapshot ?: $this->treasurerUser?->rank?->code,
        ];
    }

    /**
     * Create snapshot of treasurer_user data
     */
    public function createTreasurerUserSnapshot()
    {
        if ($this->treasurer_user_id) {
            // Load the treasurer user with relationships
            $treasurerUser = User::with(['position', 'unit', 'rank'])->find($this->treasurer_user_id);
            
            if ($treasurerUser) {
                $this->update([
                    'treasurer_user_name_snapshot' => $treasurerUser->name,
                    'treasurer_user_gelar_depan_snapshot' => $treasurerUser->gelar_depan,
                    'treasurer_user_gelar_belakang_snapshot' => $treasurerUser->gelar_belakang,
                    'treasurer_user_nip_snapshot' => $treasurerUser->nip,
                    'treasurer_user_unit_id_snapshot' => $treasurerUser->unit_id,
                    'treasurer_user_unit_name_snapshot' => $treasurerUser->unit?->name,
                    'treasurer_user_position_id_snapshot' => $treasurerUser->position_id,
                    'treasurer_user_position_name_snapshot' => $treasurerUser->position?->name,
                    'treasurer_user_position_desc_snapshot' => $treasurerUser->position_desc,
                    'treasurer_user_rank_id_snapshot' => $treasurerUser->rank_id,
                    'treasurer_user_rank_name_snapshot' => $treasurerUser->rank?->name,
                    'treasurer_user_rank_code_snapshot' => $treasurerUser->rank?->code,
                ]);
            }
        }
    }
}

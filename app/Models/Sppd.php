<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sppd extends Model
{
    use HasFactory, SoftDeletes;

    // Explicitly map to singular table name
    protected $table = 'sppd';

    protected $fillable = [
        'doc_no', 'number_is_manual', 'number_manual_reason', 'number_format_id', 'number_sequence_id',
        'number_scope_unit_id', 'sppd_date', 'spt_id', 'signed_by_user_id', 'sub_keg_id', 'assignment_title',
        'funding_source',
        // Snapshot fields for signed_by_user
        'signed_by_user_name_snapshot', 'signed_by_user_gelar_depan_snapshot', 'signed_by_user_gelar_belakang_snapshot',
        'signed_by_user_nip_snapshot', 'signed_by_user_unit_id_snapshot', 'signed_by_user_unit_name_snapshot',
        'signed_by_user_position_id_snapshot', 'signed_by_user_position_name_snapshot', 'signed_by_user_position_desc_snapshot',
        'signed_by_user_rank_id_snapshot', 'signed_by_user_rank_name_snapshot', 'signed_by_user_rank_code_snapshot',
        'signed_by_user_position_echelon_id_snapshot',
    ];

    public function spt() { return $this->belongsTo(Spt::class); }
    public function signedByUser() { return $this->belongsTo(User::class, 'signed_by_user_id'); }
    public function subKeg() { return $this->belongsTo(SubKeg::class, 'sub_keg_id'); }
    // Accessor methods untuk origin place dan destination city
    public function getOriginPlaceAttribute()
    {
        return $this->spt?->notaDinas?->originPlace;
    }
    
    public function getDestinationCityAttribute()
    {
        return $this->spt?->notaDinas?->destinationCity;
    }

    // Accessor untuk trip_type (mengambil dari NotaDinas)
    public function getTripTypeAttribute()
    {
        return $this->spt?->notaDinas?->trip_type ?? 'LUAR_DAERAH';
    }
    public function transportModes() { return $this->belongsToMany(TransportMode::class, 'sppd_transport_modes'); }
    public function itineraries() { return $this->hasMany(SppdItinerary::class); }
    public function divisumSignoffs() { return $this->hasMany(SppdDivisumSignoff::class); }
    public function receipt() { return $this->hasOne(Receipt::class); }
    public function receipts() { return $this->hasMany(Receipt::class); }
    // TripReport terkait dengan SPT, bukan SPPD langsung
    // public function tripReport() { return $this->hasOne(TripReport::class); }

    /**
     * Get all participants snapshot data from Nota Dinas
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
                'position_echelon_id' => $participant->user_position_echelon_id_snapshot ?: $participant->user?->position?->echelon?->id,
            ];
        });
    }

    /**
     * Get sorted participants snapshot data from Nota Dinas
     */
    public function getSortedParticipantsSnapshot()
    {
        if (!$this->spt?->notaDinas) {
            return collect();
        }

        return $this->spt->notaDinas->getSortedParticipants()->map(function ($participant) {
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
                'travel_grade_id' => $participant->user_travel_grade_id_snapshot ?: $participant->user?->travel_grade_id,
                'travel_grade_code' => $participant->user_travel_grade_code_snapshot ?: $participant->user?->travelGrade?->code,
                'travel_grade_name' => $participant->user_travel_grade_name_snapshot ?: $participant->user?->travelGrade?->name,
            ];
        });
    }

    /**
     * Get snapshot data for signed_by_user from SPPD snapshot
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
            'position_echelon_id' => $this->signed_by_user_position_echelon_id_snapshot ?: $this->signedByUser?->position?->echelon_id,
        ];
    }

    /**
     * Create snapshot of signed_by_user data
     */
    public function createSignedByUserSnapshot()
    {
        if (!$this->signed_by_user_id) {
            return;
        }

        $user = User::with(['position', 'unit', 'rank'])->find($this->signed_by_user_id);
        if (!$user) {
            return;
        }

        $this->update([
            'signed_by_user_name_snapshot' => $user->name,
            'signed_by_user_gelar_depan_snapshot' => $user->gelar_depan,
            'signed_by_user_gelar_belakang_snapshot' => $user->gelar_belakang,
            'signed_by_user_nip_snapshot' => $user->nip,
            'signed_by_user_unit_id_snapshot' => $user->unit_id,
            'signed_by_user_unit_name_snapshot' => $user->unit?->name,
            'signed_by_user_position_id_snapshot' => $user->position_id,
            'signed_by_user_position_name_snapshot' => $user->position?->name,
            'signed_by_user_position_desc_snapshot' => $user->position_desc,
            'signed_by_user_rank_id_snapshot' => $user->rank_id,
            'signed_by_user_rank_name_snapshot' => $user->rank?->name,
            'signed_by_user_rank_code_snapshot' => $user->rank?->code,
            'signed_by_user_position_echelon_id_snapshot' => $user->position?->echelon_id,
        ]);
    }

    /**
     * Get travel grade snapshot from Nota Dinas via SPT
     */
    public function getTravelGradeSnapshot()
    {
        if (!$this->spt?->notaDinas) {
            return null;
        }

        // Get travel grade from the first participant after sorting
        $sortedParticipants = $this->spt->notaDinas->getSortedParticipants();
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

    /**
     * Get PPTK from sub kegiatan
     */
    public function getPptkFromSubKegiatan()
    {
        return $this->subKeg?->pptkUser;
    }

    /**
     * Get PPTK snapshot data from sub kegiatan
     */
    public function getPptkSnapshotFromSubKegiatan()
    {
        $pptkUser = $this->getPptkFromSubKegiatan();
        if (!$pptkUser) {
            return null;
        }

        return [
            'name' => $pptkUser->name,
            'gelar_depan' => $pptkUser->gelar_depan,
            'gelar_belakang' => $pptkUser->gelar_belakang,
            'nip' => $pptkUser->nip,
            'unit_id' => $pptkUser->unit_id,
            'unit_name' => $pptkUser->unit?->name,
            'position_id' => $pptkUser->position_id,
            'position_name' => $pptkUser->position?->name,
            'position_desc' => $pptkUser->position_desc,
            'rank_id' => $pptkUser->rank_id,
            'rank_name' => $pptkUser->rank?->name,
            'rank_code' => $pptkUser->rank?->code,
            'position_echelon_id' => $pptkUser->position?->echelon_id,
        ];
    }

}

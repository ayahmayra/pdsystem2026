<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'doc_no' => $this->doc_no,
            'spt_date' => $this->spt_date?->format('Y-m-d'),
            'assignment_title' => $this->assignment_title,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'days_count' => $this->days_count,
            'funding_source' => $this->funding_source,
            'status' => $this->status,
            'notes' => $this->notes,
            'nota_dinas_id' => $this->nota_dinas_id,
            'signed_by_user_id' => $this->signed_by_user_id,
            'signed_by_user' => $this->whenLoaded('signedByUser', fn() => new UserResource($this->signedByUser)),
            'members' => $this->whenLoaded('members', fn() => SptMemberResource::collection($this->members)),
            'members_count' => $this->whenCounted('members'),
            // Snapshot data untuk signed_by_user
            'signed_by_user_snapshot' => $this->when(
                $this->signed_by_user_name_snapshot,
                fn() => [
                    'name' => $this->signed_by_user_name_snapshot,
                    'gelar_depan' => $this->signed_by_user_gelar_depan_snapshot,
                    'gelar_belakang' => $this->signed_by_user_gelar_belakang_snapshot,
                    'nip' => $this->signed_by_user_nip_snapshot,
                    'unit_id' => $this->signed_by_user_unit_id_snapshot,
                    'unit_name' => $this->signed_by_user_unit_name_snapshot,
                    'position_id' => $this->signed_by_user_position_id_snapshot,
                    'position_name' => $this->signed_by_user_position_name_snapshot,
                    'position_desc' => $this->signed_by_user_position_desc_snapshot,
                    'rank_id' => $this->signed_by_user_rank_id_snapshot,
                    'rank_name' => $this->signed_by_user_rank_name_snapshot,
                    'rank_code' => $this->signed_by_user_rank_code_snapshot,
                ]
            ),
        ];
    }
}

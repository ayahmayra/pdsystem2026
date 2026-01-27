<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'nip' => $this->nip,
            'nik' => $this->nik,
            'gelar_depan' => $this->gelar_depan,
            'gelar_belakang' => $this->gelar_belakang,
            'employee_type' => $this->employee_type,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'address' => $this->address,
            'unit' => $this->whenLoaded('unit', fn() => new UnitResource($this->unit)),
            'instansi' => $this->whenLoaded('instansi', fn() => new InstansiResource($this->instansi)),
            'position' => $this->whenLoaded('position', fn() => new PositionResource($this->position)),
            'position_desc' => $this->position_desc,
            'rank' => $this->whenLoaded('rank', fn() => new RankResource($this->rank)),
            'travel_grade' => $this->whenLoaded('travelGrade', fn() => new TravelGradeResource($this->travelGrade)),
            'npwp' => $this->npwp,
            'bank_name' => $this->bank_name,
            'bank_account_no' => $this->bank_account_no,
            'bank_account_name' => $this->bank_account_name,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'gender' => $this->gender,
            'is_signer' => $this->is_signer,
            'is_non_staff' => $this->is_non_staff,
            'budget_user_role' => $this->budget_user_role,
            'budget_user_role_label' => $this->getBudgetUserRoleLabel(),
            'full_name_with_titles' => $this->fullNameWithTitles(),
            'nip_label' => $this->getNipLabel(),
        ];
    }
}

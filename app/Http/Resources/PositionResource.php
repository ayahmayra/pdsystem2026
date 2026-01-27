<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PositionResource extends JsonResource
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
            'type' => $this->type,
            'echelon_id' => $this->echelon_id,
            'echelon' => $this->whenLoaded('echelon', fn() => new EchelonResource($this->echelon)),
            'full_name' => $this->fullName(),
            'echelon_display' => $this->getEchelonDisplay(),
            'users_count' => $this->whenCounted('users'),
        ];
    }
}

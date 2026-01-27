<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SptMemberResource extends JsonResource
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
            'spt_id' => $this->spt_id,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
            'spt' => $this->whenLoaded('spt', fn() => new SptResource($this->spt)),
        ];
    }
}

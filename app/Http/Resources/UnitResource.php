<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'parent' => $this->whenLoaded('parent', fn() => new UnitResource($this->parent)),
            'children' => $this->whenLoaded('children', fn() => UnitResource::collection($this->children)),
            'full_name' => $this->fullName(),
            'users_count' => $this->whenCounted('users'),
            'children_count' => $this->whenCounted('children'),
        ];
    }
}

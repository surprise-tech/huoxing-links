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
            'status' => $this->status,
            'username' => $this->username,
            'type' => $this->type,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'parent' => new UserSimpleResource($this->whenLoaded('parent')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'login_time' => $this->login_time?->format('Y-m-d H:i:s'),
        ];
    }
}

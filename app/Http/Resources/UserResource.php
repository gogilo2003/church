<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'phone_number' => $this->phone_number,
            'status' => $this->status ?? 'active',
            'is_admin' => (bool) $this->is_admin,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'role_ids' => $this->whenLoaded('roles', fn () => $this->roles->pluck('id')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

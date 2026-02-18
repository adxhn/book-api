<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
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
            'ip_address' => $this->ip_address,
            'last_used_ago' => optional($this->last_used_at)->diffForHumans(),
            'device_id' => $this->device_id,
            'device_model' => $this->device_model,
            'device_type' => $this->device_type
        ];
    }
}

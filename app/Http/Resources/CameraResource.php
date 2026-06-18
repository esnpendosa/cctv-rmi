<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CameraResource
 * 
 * Maps camera model to JSON response for API endpoints.
 * 
 * @package App\Http\Resources
 */
class CameraResource extends JsonResource
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
            'brand' => $this->brand,
            'model' => $this->model,
            'ip_address' => $this->ip_address,
            'stream_key' => $this->stream_key,
            'stream_type' => $this->stream_type,
            'access' => $this->access,
            'status' => $this->status,
            'location' => $this->location ? $this->location->name : null,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'last_online_at' => $this->last_online_at,
            'last_offline_at' => $this->last_offline_at,
        ];
    }
}

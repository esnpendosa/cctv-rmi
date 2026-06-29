<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MonitoringLogResource
 * 
 * Maps monitoring log model to JSON response for API endpoints.
 * 
 * @package App\Http\Resources
 */
class MonitoringLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'camera_id' => $this->camera_id,
            'camera_name' => $this->camera ? $this->camera->name : null,
            'event_type' => $this->event_type,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'recorded_at' => $this->recorded_at,
            'created_at' => $this->created_at,
        ];
    }
}

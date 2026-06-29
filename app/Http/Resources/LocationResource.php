<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class LocationResource
 * 
 * Maps Location model to JSON response for API endpoints.
 * 
 * @package App\Http\Resources
 */
class LocationResource extends JsonResource
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
            'name' => $this->name,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'description' => $this->description,
            'cameras_count' => $this->whenCounted('cameras'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

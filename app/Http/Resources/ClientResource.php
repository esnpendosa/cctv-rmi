<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ClientResource
 * 
 * Maps Client model to JSON response for API endpoints.
 * 
 * @package App\Http\Resources
 */
class ClientResource extends JsonResource
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
            'company' => $this->company,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'npwp' => $this->npwp,
            'notes' => $this->notes,
            'locations_count' => $this->whenCounted('locations'),
            'invoices_count' => $this->whenCounted('invoices'),
            'quotations_count' => $this->whenCounted('quotations'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

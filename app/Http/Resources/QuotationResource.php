<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class QuotationResource
 * 
 * Maps quotation model to JSON response for API endpoints.
 * 
 * @package App\Http\Resources
 */
class QuotationResource extends JsonResource
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
            'number' => $this->number,
            'client_id' => $this->client_id,
            'client_name' => $this->client ? $this->client->name : null,
            'client_company' => $this->client ? $this->client->company : null,
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'valid_until' => $this->valid_until,
            'notes' => $this->notes,
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
            'tax_percent' => $this->tax_percent,
            'tax_amount' => $this->tax_amount,
            'total' => $this->total,
            'created_by_name' => $this->creator ? $this->creator->name : null,
            'items' => QuotationItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

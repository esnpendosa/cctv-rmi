<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class InvoiceItemResource
 * 
 * Maps invoice line item to JSON response.
 * 
 * @package App\Http\Resources
 */
class InvoiceItemResource extends JsonResource
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
            'description' => $this->description,
            'qty' => $this->qty,
            'unit_price' => $this->unit_price,
            'discount_percent' => $this->discount_percent,
            'subtotal' => $this->subtotal,
        ];
    }
}

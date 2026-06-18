<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class InventoryResource
 * 
 * Maps inventory model to JSON response for API endpoints.
 * 
 * @package App\Http\Resources
 */
class InventoryResource extends JsonResource
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
            'sku' => $this->sku,
            'name' => $this->name,
            'category' => $this->category,
            'stock' => $this->stock,
            'selling_price' => $this->selling_price,
            'condition' => $this->condition,
        ];
    }
}

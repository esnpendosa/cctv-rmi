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
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'category' => $this->category,
            'brand' => $this->brand,
            'model' => $this->model,
            'serial_number' => $this->serial_number,
            'stock' => $this->stock,
            'min_stock' => $this->min_stock,
            'purchase_price' => $this->purchase_price,
            'selling_price' => $this->selling_price,
            'unit' => $this->unit,
            'condition' => $this->condition instanceof \BackedEnum ? $this->condition->value : $this->condition,
            'location' => $this->location ? $this->location->name : null,
            'photo' => $this->photo ? url($this->photo) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

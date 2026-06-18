<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class InvoiceResource
 * 
 * Maps invoice model to JSON response for API endpoints.
 * 
 * @package App\Http\Resources
 */
class InvoiceResource extends JsonResource
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
            'number' => $this->number,
            'client' => $this->client ? $this->client->name : null,
            'company' => $this->client ? $this->client->company : null,
            'status' => $this->status,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
            'total' => $this->total,
        ];
    }
}

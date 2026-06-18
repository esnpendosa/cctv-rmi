<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class QuotationItem
 * 
 * Represents an item in a quotation.
 * 
 * @property int $id
 * @property int $quotation_id
 * @property int|null $inventory_id
 * @property string $description
 * @property int $qty
 * @property float $unit_price
 * @property float $discount_percent
 * @property float $subtotal
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \App\Models\Quotation $quotation
 * @property-read \App\Models\Inventory|null $inventory
 * 
 * @package App\Models
 */
class QuotationItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quotation_id',
        'inventory_id',
        'description',
        'qty',
        'unit_price',
        'discount_percent',
        'subtotal',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'qty' => 'integer',
        'unit_price' => 'float',
        'discount_percent' => 'float',
        'subtotal' => 'float',
    ];

    /**
     * Get the quotation this item belongs to.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    /**
     * Get the inventory item associated with this quotation item.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class InvoiceItem
 * 
 * Represents an item in an invoice.
 * 
 * @property int $id
 * @property int $invoice_id
 * @property int|null $inventory_id
 * @property string $description
 * @property int $qty
 * @property float $unit_price
 * @property float $discount_percent
 * @property float $subtotal
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \App\Models\Invoice $invoice
 * @property-read \App\Models\Inventory|null $inventory
 * 
 * @package App\Models
 */
class InvoiceItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',
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
     * Get the invoice this item belongs to.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the inventory item associated with this invoice item.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}

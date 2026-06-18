<?php

namespace App\Models;

use App\Enums\InventoryCondition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Inventory
 * 
 * Represents an inventory item in stock.
 * 
 * @property int $id
 * @property string $sku
 * @property string $name
 * @property string $category
 * @property string $brand
 * @property string $model
 * @property string|null $serial_number
 * @property int $stock
 * @property int $min_stock
 * @property float $purchase_price
 * @property float $selling_price
 * @property string $unit
 * @property InventoryCondition $condition
 * @property int|null $location_id
 * @property string|null $photo
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \App\Models\Location|null $location
 * 
 * @package App\Models
 */
class Inventory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sku',
        'name',
        'category',
        'brand',
        'model',
        'serial_number',
        'stock',
        'min_stock',
        'purchase_price',
        'selling_price',
        'unit',
        'condition',
        'location_id',
        'photo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stock' => 'integer',
        'min_stock' => 'integer',
        'purchase_price' => 'float',
        'selling_price' => 'float',
        'condition' => InventoryCondition::class,
    ];

    /**
     * Get the location where this inventory is stored.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}

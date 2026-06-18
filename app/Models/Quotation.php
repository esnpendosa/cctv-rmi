<?php

namespace App\Models;

use App\Enums\QuotationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Quotation
 * 
 * Represents a client price quotation.
 * 
 * @property int $id
 * @property string $number
 * @property int $client_id
 * @property QuotationStatus $status
 * @property \Carbon\Carbon $valid_until
 * @property string|null $notes
 * @property float $subtotal
 * @property float $discount_amount
 * @property float $tax_percent
 * @property float $tax_amount
 * @property float $total
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\QuotationItem[] $items
 * 
 * @package App\Models
 */
class Quotation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'number',
        'client_id',
        'status',
        'valid_until',
        'notes',
        'subtotal',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'total',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => QuotationStatus::class,
        'valid_until' => 'date',
        'subtotal' => 'float',
        'discount_amount' => 'float',
        'tax_percent' => 'float',
        'tax_amount' => 'float',
        'total' => 'float',
    ];

    /**
     * Get the client associated with the quotation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user who created the quotation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the items in this quotation.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class CameraCategory
 * 
 * Represents a camera classification category.
 * 
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $color
 * @property string $icon
 * @property string|null $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Camera[] $cameras
 * 
 * @package App\Models
 */
class CameraCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'color',
        'icon',
        'description',
    ];

    /**
     * Get the cameras in this category.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cameras(): HasMany
    {
        return $this->hasMany(Camera::class, 'category_id');
    }
}

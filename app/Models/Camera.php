<?php

namespace App\Models;

use App\Enums\CameraAccess;
use App\Enums\CameraStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Camera
 * 
 * Represents a CCTV camera device.
 * 
 * @property int $id
 * @property string $name
 * @property string $brand
 * @property string $model
 * @property string $ip_address
 * @property string $rtsp_url (Encrypted at rest)
 * @property string $stream_key
 * @property string $stream_type
 * @property CameraAccess $access
 * @property CameraStatus $status
 * @property int $category_id
 * @property int $location_id
 * @property float $latitude
 * @property float $longitude
 * @property \Carbon\Carbon $installation_date
 * @property \Carbon\Carbon|null $warranty_until
 * @property \Carbon\Carbon|null $last_online_at
 * @property \Carbon\Carbon|null $last_offline_at
 * @property string|null $public_token
 * @property string|null $notes
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \App\Models\CameraCategory $category
 * @property-read \App\Models\Location $location
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MonitoringLog[] $monitoringLogs
 * 
 * @package App\Models
 */
class Camera extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'brand',
        'model',
        'ip_address',
        'rtsp_url',
        'stream_key',
        'stream_type',
        'access',
        'status',
        'category_id',
        'location_id',
        'latitude',
        'longitude',
        'installation_date',
        'warranty_until',
        'last_online_at',
        'last_offline_at',
        'public_token',
        'notes',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rtsp_url' => 'encrypted',
        'access' => CameraAccess::class,
        'status' => CameraStatus::class,
        'latitude' => 'float',
        'longitude' => 'float',
        'installation_date' => 'date',
        'warranty_until' => 'date',
        'last_online_at' => 'datetime',
        'last_offline_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category that this camera belongs to.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CameraCategory::class, 'category_id');
    }

    /**
     * Get the location where this camera is installed.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the monitoring logs for this camera.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function monitoringLogs(): HasMany
    {
        return $this->hasMany(MonitoringLog::class);
    }
}

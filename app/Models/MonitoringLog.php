<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class MonitoringLog
 * 
 * Represents a log entry for a camera event.
 * 
 * @property int $id
 * @property int $camera_id
 * @property string $event_type
 * @property string $description
 * @property array|null $metadata
 * @property \Carbon\Carbon $recorded_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \App\Models\Camera $camera
 * 
 * @package App\Models
 */
class MonitoringLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'camera_id',
        'event_type',
        'description',
        'metadata',
        'recorded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'recorded_at' => 'datetime',
    ];

    /**
     * Get the camera associated with this monitoring log.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function camera(): BelongsTo
    {
        return $this->belongsTo(Camera::class);
    }
}

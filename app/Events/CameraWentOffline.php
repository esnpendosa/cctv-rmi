<?php

namespace App\Events;

use App\Models\Camera;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class CameraWentOffline
 * 
 * Fired when a camera is detected as offline.
 * 
 * @package App\Events
 */
class CameraWentOffline
{
    use Dispatchable, SerializesModels;

    /**
     * @var Camera
     */
    public Camera $camera;

    /**
     * Create a new event instance.
     * 
     * @param Camera $camera
     */
    public function __construct(Camera $camera)
    {
        $this->camera = $camera;
    }
}

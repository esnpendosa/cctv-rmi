<?php

namespace App\Enums;

/**
 * Class CameraStatus
 * 
 * Represents the operational status of a camera.
 * 
 * @package App\Enums
 */
enum CameraStatus: string
{
    case Online = 'online';
    case Offline = 'offline';
    case Maintenance = 'maintenance';
}

<?php

namespace App\Enums;

/**
 * Class CameraAccess
 * 
 * Represents the access control level of a camera.
 * 
 * @package App\Enums
 */
enum CameraAccess: string
{
    case Public = 'public';
    case Private = 'private';
}

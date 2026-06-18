<?php

namespace App\Services;

use App\Enums\CameraAccess;
use App\Models\Camera;
use App\Repositories\Interfaces\CameraRepositoryInterface;
use Illuminate\Support\Str;

/**
 * Class CameraService
 * 
 * Handles business logic for camera management.
 * 
 * @package App\Services
 */
class CameraService
{
    /**
     * @var CameraRepositoryInterface
     */
    protected CameraRepositoryInterface $cameraRepository;

    /**
     * CameraService constructor.
     * 
     * @param CameraRepositoryInterface $cameraRepository
     */
    public function __construct(CameraRepositoryInterface $cameraRepository)
    {
        $this->cameraRepository = $cameraRepository;
    }

    /**
     * Enable or disable public access for a camera.
     * 
     * @param Camera $camera
     * @param bool $isPublic
     * @return Camera
     */
    public function setPublicAccess(Camera $camera, bool $isPublic): Camera
    {
        if ($isPublic && empty($camera->public_token)) {
            $camera->public_token = (string) Str::uuid();
        }
        $camera->access = $isPublic 
            ? CameraAccess::Public 
            : CameraAccess::Private;
        $camera->save();

        return $camera;
    }

    /**
     * Revoke public access by changing token and setting access to private.
     * 
     * @param Camera $camera
     * @return Camera
     */
    public function revokePublicAccess(Camera $camera): Camera
    {
        $camera->public_token = (string) Str::uuid(); // regenerate = old URL invalid
        $camera->access = CameraAccess::Private;
        $camera->save();

        return $camera;
    }
}

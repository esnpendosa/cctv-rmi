<?php

namespace App\Listeners;

use App\Events\CameraWentOffline;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class SendCameraOfflineNotification
 * 
 * Handles the CameraWentOffline event by sending notifications.
 * 
 * @package App\Listeners
 */
class SendCameraOfflineNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var NotificationService
     */
    protected NotificationService $notificationService;

    /**
     * Create the event listener.
     * 
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     * 
     * @param CameraWentOffline $event
     * @return void
     */
    public function handle(CameraWentOffline $event): void
    {
        $this->notificationService->notifyCameraOffline($event->camera);
    }
}

<?php

namespace App\Listeners;

use App\Events\CameraWentOnline;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class SendCameraOnlineNotification
 * 
 * Handles the CameraWentOnline event by sending notifications.
 * 
 * @package App\Listeners
 */
class SendCameraOnlineNotification implements ShouldQueue
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
     * @param CameraWentOnline $event
     * @return void
     */
    public function handle(CameraWentOnline $event): void
    {
        $this->notificationService->notifyCameraOnline($event->camera);
    }
}

<?php

namespace App\Services;

use App\Models\Camera;
use App\Models\Invoice;
use App\Models\User;
use App\Notifications\CameraOfflineNotification;
use App\Notifications\CameraOnlineNotification;
use App\Notifications\InvoiceOverdueNotification;
use Illuminate\Support\Facades\Notification;

/**
 * Class NotificationService
 * 
 * Manages dispatching notifications to appropriate user roles.
 * 
 * @package App\Services
 */
class NotificationService
{
    /**
     * Notify technician and administrative roles that a camera went offline.
     * 
     * @param Camera $camera
     * @return void
     */
    public function notifyCameraOffline(Camera $camera): void
    {
        // Fetch users who should know about hardware failure
        $recipients = User::role(['Super Admin', 'Admin', 'Technician'])->get();
        
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new CameraOfflineNotification($camera));
        }
    }

    /**
     * Notify technician and administrative roles that a camera is online.
     * 
     * @param Camera $camera
     * @return void
     */
    public function notifyCameraOnline(Camera $camera): void
    {
        $recipients = User::role(['Super Admin', 'Admin', 'Technician'])->get();
        
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new CameraOnlineNotification($camera));
        }
    }

    /**
     * Notify finance roles that an invoice is overdue.
     * 
     * @param Invoice $invoice
     * @return void
     */
    public function notifyInvoiceOverdue(Invoice $invoice): void
    {
        // Fetch users in finance roles
        $recipients = User::role(['Super Admin', 'Finance'])->get();
        
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new InvoiceOverdueNotification($invoice));
        }
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

/**
 * Class NotificationDropdown
 * 
 * Handles real-time system notifications in the header.
 * 
 * @package App\Livewire
 */
class NotificationDropdown extends Component
{
    /**
     * Get the current user's unread notifications.
     */
    public function getNotificationsProperty()
    {
        return Auth::user() ? Auth::user()->unreadNotifications()->limit(5)->get() : collect();
    }

    /**
     * Get the current user's unread notifications count.
     */
    public function getUnreadCountProperty()
    {
        return Auth::user() ? Auth::user()->unreadNotifications()->count() : 0;
    }

    /**
     * Mark a specific notification as read.
     * 
     * @param string $id
     * @return void
     */
    public function markAsRead(string $id): void
    {
        if (Auth::user()) {
            $notification = Auth::user()->notifications()->find($id);
            if ($notification) {
                $notification->markAsRead();
            }
        }
    }

    /**
     * Mark all notifications as read.
     * 
     * @return void
     */
    public function markAllAsRead(): void
    {
        if (Auth::user()) {
            Auth::user()->unreadNotifications->markAsRead();
        }
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.notification-dropdown', [
            'notifications' => $this->notifications,
            'unreadCount' => $this->unreadCount,
        ]);
    }
}

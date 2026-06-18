<?php

namespace App\Notifications;

use App\Models\Camera;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class CameraOfflineNotification
 * 
 * Notifies users when a camera goes offline.
 * 
 * @package App\Notifications
 */
class CameraOfflineNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Camera
     */
    protected Camera $camera;

    /**
     * Create a new notification instance.
     * 
     * @param Camera $camera
     */
    public function __construct(Camera $camera)
    {
        $this->camera = $camera;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Standard delivery channels: database and mail
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     * 
     * @param object $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject('Peringatan: Kamera CCTV Offline - ' . $this->camera->name)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Kamera CCTV berikut dilaporkan offline oleh sistem pemantauan:')
            ->line('**Nama Kamera:** ' . $this->camera->name)
            ->line('**Brand/Model:** ' . $this->camera->brand . ' / ' . $this->camera->model)
            ->line('**IP Address:** ' . $this->camera->ip_address)
            ->line('**Lokasi:** ' . ($this->camera->location ? $this->camera->location->name : 'N/A'))
            ->line('**Waktu Deteksi:** ' . ($this->camera->last_offline_at ? $this->camera->last_offline_at->format('d M Y H:i:s') : now()->format('d M Y H:i:s')))
            ->action('Lihat Detail Kamera', url('/cameras/' . $this->camera->id))
            ->line('Harap segera lakukan pengecekan fisik atau konektivitas jaringan.');
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'camera_id' => $this->camera->id,
            'camera_name' => $this->camera->name,
            'ip_address' => $this->camera->ip_address,
            'location_name' => $this->camera->location ? $this->camera->location->name : 'N/A',
            'event' => 'offline',
            'title' => 'Kamera CCTV Offline',
            'message' => "Kamera {$this->camera->name} di lokasi " . ($this->camera->location ? $this->camera->location->name : 'N/A') . " terdeteksi offline.",
        ];
    }
}

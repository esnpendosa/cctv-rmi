<?php

namespace App\Services;

use App\Enums\CameraStatus;
use App\Events\CameraWentOffline;
use App\Events\CameraWentOnline;
use App\Models\Camera;
use App\Models\MonitoringLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class CameraHealthService
 * 
 * Pings the go2rtc REST API to check the status of camera streams.
 * 
 * @package App\Services
 */
class CameraHealthService
{
    /**
     * Check the health of all active cameras via the go2rtc API.
     * 
     * @return void
     */
    public function checkHealth(): void
    {
        $host = config('cctv.go2rtc_host', 'localhost');
        $port = config('cctv.go2rtc_port', 1984);
        $url = "http://{$host}:{$port}/api/streams";

        try {
            $response = Http::timeout(5)->get($url);

            if ($response->successful()) {
                $streams = $response->json();
                $activeKeys = is_array($streams) ? array_keys($streams) : [];

                $cameras = Camera::where('is_active', true)->get();
                foreach ($cameras as $camera) {
                    $isOnline = in_array($camera->stream_key, $activeKeys);
                    $this->updateCameraStatus($camera, $isOnline);
                }
            } else {
                Log::warning("go2rtc health check failed: Server returned code " . $response->status());
                $this->handleGo2rtcDown("Server returned status " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("go2rtc health check exception: " . $e->getMessage());
            $this->handleGo2rtcDown($e->getMessage());
        }
    }

    /**
     * Update camera status and dispatch events/logs if status changed.
     * 
     * @param Camera $camera
     * @param bool $isOnline
     * @return void
     */
    protected function updateCameraStatus(Camera $camera, bool $isOnline): void
    {
        $oldStatus = $camera->status;
        $newStatus = $isOnline ? CameraStatus::Online : CameraStatus::Offline;

        // Do not override maintenance status automatically
        if ($oldStatus === CameraStatus::Maintenance) {
            return;
        }

        if ($oldStatus !== $newStatus) {
            $camera->status = $newStatus;
            
            if ($isOnline) {
                $camera->last_online_at = now();
                $camera->save();
                
                event(new CameraWentOnline($camera));

                MonitoringLog::create([
                    'camera_id' => $camera->id,
                    'event_type' => 'online',
                    'description' => "Kamera {$camera->name} kembali online.",
                    'metadata' => ['source' => 'health_check'],
                    'recorded_at' => now(),
                ]);
            } else {
                $camera->last_offline_at = now();
                $camera->save();

                event(new CameraWentOffline($camera));

                MonitoringLog::create([
                    'camera_id' => $camera->id,
                    'event_type' => 'offline',
                    'description' => "Kamera {$camera->name} terdeteksi offline.",
                    'metadata' => ['source' => 'health_check', 'reason' => 'Not found in go2rtc streams list'],
                    'recorded_at' => now(),
                ]);
            }
        }
    }

    /**
     * Set all online cameras to offline if the go2rtc server is unreachable.
     * 
     * @param string $errorMsg
     * @return void
     */
    protected function handleGo2rtcDown(string $errorMsg = ''): void
    {
        $cameras = Camera::where('is_active', true)
            ->where('status', CameraStatus::Online)
            ->get();

        foreach ($cameras as $camera) {
            $camera->status = CameraStatus::Offline;
            $camera->last_offline_at = now();
            $camera->save();

            event(new CameraWentOffline($camera));

            MonitoringLog::create([
                'camera_id' => $camera->id,
                'event_type' => 'error',
                'description' => "Kamera {$camera->name} offline karena media server go2rtc tidak dapat dihubungi.",
                'metadata' => ['source' => 'health_check', 'error' => $errorMsg],
                'recorded_at' => now(),
            ]);
        }
    }
}

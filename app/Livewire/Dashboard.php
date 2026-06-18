<?php

namespace App\Livewire;

use App\Models\Camera;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\MonitoringLog;
use App\Enums\CameraStatus;
use Livewire\Component;

/**
 * Class Dashboard
 * 
 * Handles CCTV monitoring and finance statistics display.
 * 
 * @package App\Livewire
 */
class Dashboard extends Component
{
    /**
     * Render the component view with stats.
     */
    public function render()
    {
        $cameraStats = [
            'total' => Camera::count(),
            'online' => Camera::where('status', CameraStatus::Online)->count(),
            'offline' => Camera::where('status', CameraStatus::Offline)->count(),
            'maintenance' => Camera::where('status', CameraStatus::Maintenance)->count(),
        ];

        $clientCount = Client::count();
        $inventoryCount = Inventory::count();
        $lowStockCount = Inventory::whereColumn('stock', '<=', 'min_stock')->count();

        // Get map locations data
        $locations = Location::with(['cameras'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'address' => $location->address,
                    'latitude' => (float) $location->latitude,
                    'longitude' => (float) $location->longitude,
                    'cameras_count' => $location->cameras->count(),
                    'online_count' => $location->cameras->where('status', CameraStatus::Online)->count(),
                    'offline_count' => $location->cameras->where('status', CameraStatus::Offline)->count(),
                ];
            });

        // Get latest 10 monitoring logs
        $latestLogs = MonitoringLog::with('camera.location')
            ->orderBy('recorded_at', 'desc')
            ->limit(10)
            ->get();

        // Get live grid cameras (first 4 online cameras to show in stream grid)
        $gridCameras = Camera::with('location')
            ->where('status', CameraStatus::Online)
            ->limit(4)
            ->get();

        return view('livewire.dashboard', [
            'cameraStats' => $cameraStats,
            'clientCount' => $clientCount,
            'inventoryCount' => $inventoryCount,
            'lowStockCount' => $lowStockCount,
            'locations' => $locations,
            'latestLogs' => $latestLogs,
            'gridCameras' => $gridCameras,
        ])->layout('layouts.app');
    }
}

<?php

namespace App\Livewire;

use App\Models\Camera;
use App\Models\Location;
use App\Enums\CameraStatus;
use Livewire\Component;

/**
 * Class MonitorWall
 *
 * Public full-screen CCTV monitoring wall.
 * Shows all cameras grouped by location with live feed embeds.
 *
 * @package App\Livewire
 */
class MonitorWall extends Component
{
    /** @var string Currently active location filter (empty = show all) */
    public string $activeLocation = '';

    /** @var string Grid layout: '1', '2', '3', '4' columns */
    public string $gridCols = '3';

    /** @var bool Auto-cycle through cameras fullscreen */
    public bool $autoPlay = false;

    /**
     * Render the public monitoring wall.
     */
    public function render()
    {
        $query = Camera::with(['location', 'category'])
            ->orderByRaw("FIELD(status, 'online', 'maintenance', 'offline')");

        if ($this->activeLocation !== '') {
            $query->where('location_id', $this->activeLocation);
        }

        $cameras   = $query->get();
        $locations = Location::orderBy('name')->get();

        $stats = [
            'total'       => Camera::count(),
            'online'      => Camera::where('status', CameraStatus::Online)->count(),
            'offline'     => Camera::where('status', CameraStatus::Offline)->count(),
            'maintenance' => Camera::where('status', CameraStatus::Maintenance)->count(),
        ];

        $go2rtcHost = config('cctv.go2rtc_host', 'localhost');
        $go2rtcPort = config('cctv.go2rtc_port', 1984);

        return view('livewire.monitor-wall', compact(
            'cameras', 'locations', 'stats', 'go2rtcHost', 'go2rtcPort'
        ))->layout('layouts.monitor');
    }
}

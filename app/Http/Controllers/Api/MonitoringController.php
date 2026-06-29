<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CameraResource;
use App\Http\Resources\MonitoringLogResource;
use App\Models\Camera;
use App\Models\MonitoringLog;
use App\Enums\CameraStatus;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Class MonitoringController
 * 
 * Handles camera health monitoring, alerts, and historical logs.
 * 
 * @package App\Http\Controllers\Api
 */
class MonitoringController extends Controller
{
    use ApiResponder;

    /**
     * Get live status of all cameras.
     * Uses Redis cache with a 30-second TTL to avoid database overhead.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function live()
    {
        $cameras = Cache::remember('api_monitoring_live', 30, function () {
            return Camera::with(['location', 'category'])->get();
        });

        return $this->successResponse(CameraResource::collection($cameras), 'Status kamera real-time berhasil diambil.');
    }

    /**
     * Get paginated status history for a specific camera.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $cameraId
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request, $cameraId)
    {
        // Ensure camera exists
        Camera::findOrFail($cameraId);

        $perPage = $request->input('per_page', 15);
        $paginator = MonitoringLog::with('camera')
            ->where('camera_id', $cameraId)
            ->orderBy('recorded_at', 'desc')
            ->paginate($perPage);

        return $this->successResponse(
            MonitoringLogResource::collection($paginator->items()),
            'Riwayat status kamera berhasil diambil.',
            200,
            [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]
        );
    }

    /**
     * Get cameras that recently went offline (in the last 1 hour).
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function alert()
    {
        $oneHourAgo = now()->subHour();

        $cameras = Camera::with(['location', 'category'])
            ->where('status', CameraStatus::Offline)
            ->where(function ($query) use ($oneHourAgo) {
                $query->where('last_offline_at', '>=', $oneHourAgo)
                      ->orWhereNull('last_offline_at'); // Include if status is offline but date is null (fallback)
            })
            ->get();

        return $this->successResponse(CameraResource::collection($cameras), 'Daftar alert kamera offline (1 jam terakhir) berhasil diambil.');
    }
}

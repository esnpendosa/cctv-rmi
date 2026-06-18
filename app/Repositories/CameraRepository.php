<?php

namespace App\Repositories;

use App\Enums\CameraStatus;
use App\Models\Camera;
use App\Models\MonitoringLog;
use App\Repositories\Interfaces\CameraRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class CameraRepository
 * 
 * Implements camera database operations using Eloquent.
 * 
 * @package App\Repositories
 */
class CameraRepository implements CameraRepositoryInterface
{
    /**
     * Get all cameras.
     * 
     * @return Collection
     */
    public function all(): Collection
    {
        return Camera::with(['category', 'location'])->get();
    }

    /**
     * Find a camera by ID.
     * 
     * @param int $id
     * @return Camera|null
     */
    public function find(int $id): ?Camera
    {
        return Camera::with(['category', 'location'])->find($id);
    }

    /**
     * Create a new camera.
     * 
     * @param array $data
     * @return Camera
     */
    public function create(array $data): Camera
    {
        return Camera::create($data);
    }

    /**
     * Update an existing camera.
     * 
     * @param int $id
     * @param array $data
     * @return Camera
     */
    public function update(int $id, array $data): Camera
    {
        $camera = Camera::findOrFail($id);
        $camera->update($data);
        return $camera;
    }

    /**
     * Delete a camera.
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $camera = Camera::find($id);
        if ($camera) {
            return (bool) $camera->delete();
        }
        return false;
    }

    /**
     * Find a camera by its public access token.
     * 
     * @param string $token
     * @return Camera|null
     */
    public function findByPublicToken(string $token): ?Camera
    {
        return Camera::with(['category', 'location'])
            ->where('public_token', $token)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get monitoring logs for a camera.
     * 
     * @param Camera $camera
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getMonitoringLogs(Camera $camera, int $perPage = 10): LengthAwarePaginator
    {
        return MonitoringLog::where('camera_id', $camera->id)
            ->orderBy('recorded_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Calculate camera uptime percentage for the last X days.
     * 
     * @param Camera $camera
     * @param int $days
     * @return float
     */
    public function getUptimePercentage(Camera $camera, int $days = 7): float
    {
        $startDate = Carbon::now()->subDays($days);
        
        $logs = MonitoringLog::where('camera_id', $camera->id)
            ->where('recorded_at', '>=', $startDate)
            ->get();

        if ($logs->isEmpty()) {
            return $camera->status === CameraStatus::Offline ? 0.0 : 100.0;
        }

        $onlineLogs = $logs->where('event_type', 'online')->count();
        $totalLogs = $logs->whereIn('event_type', ['online', 'offline', 'error'])->count();

        if ($totalLogs === 0) {
            return $camera->status === CameraStatus::Offline ? 0.0 : 100.0;
        }

        return round(($onlineLogs / $totalLogs) * 100, 2);
    }
}

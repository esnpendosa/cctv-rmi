<?php

namespace App\Repositories\Interfaces;

use App\Models\Camera;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface CameraRepositoryInterface
 * 
 * Defines the contract for camera persistence operations.
 * 
 * @package App\Repositories\Interfaces
 */
interface CameraRepositoryInterface
{
    /**
     * Get all cameras.
     * 
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Find a camera by ID.
     * 
     * @param int $id
     * @return Camera|null
     */
    public function find(int $id): ?Camera;

    /**
     * Create a new camera.
     * 
     * @param array $data
     * @return Camera
     */
    public function create(array $data): Camera;

    /**
     * Update an existing camera.
     * 
     * @param int $id
     * @param array $data
     * @return Camera
     */
    public function update(int $id, array $data): Camera;

    /**
     * Delete a camera.
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Find a camera by its public access token.
     * 
     * @param string $token
     * @return Camera|null
     */
    public function findByPublicToken(string $token): ?Camera;

    /**
     * Get monitoring logs for a camera.
     * 
     * @param Camera $camera
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getMonitoringLogs(Camera $camera, int $perPage = 10): LengthAwarePaginator;

    /**
     * Calculate camera uptime percentage for the last X days.
     * 
     * @param Camera $camera
     * @param int $days
     * @return float
     */
    public function getUptimePercentage(Camera $camera, int $days = 7): float;
}

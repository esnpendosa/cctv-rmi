<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CameraResource;
use App\Http\Resources\LocationResource;
use App\Models\Camera;
use App\Models\Location;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;

/**
 * Class AreaController
 * 
 * Handles endpoints related to camera locations (areas).
 * 
 * @package App\Http\Controllers\Api
 */
class AreaController extends Controller
{
    use ApiResponder;

    /**
     * Display a listing of areas (locations) with their camera counts.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $locations = Location::withCount('cameras')->get();

        return $this->successResponse(LocationResource::collection($locations), 'Daftar area berhasil diambil.');
    }

    /**
     * Display cameras inside a specific area.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cameras($id)
    {
        $location = Location::findOrFail($id);
        $cameras = Camera::with(['location', 'category'])
            ->where('location_id', $id)
            ->get();

        return $this->successResponse(CameraResource::collection($cameras), "Daftar kamera di area {$location->name} berhasil diambil.");
    }
}

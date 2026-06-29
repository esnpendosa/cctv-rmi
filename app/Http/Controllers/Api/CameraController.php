<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CameraResource;
use App\Models\Camera;
use App\Models\Location;
use App\Enums\CameraStatus;
use App\Events\CameraWentOffline;
use App\Events\CameraWentOnline;
use App\Models\MonitoringLog;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Class CameraController
 * 
 * Handles camera management and streaming endpoints for mobile app.
 * 
 * @package App\Http\Controllers\Api
 */
class CameraController extends Controller
{
    use ApiResponder;

    /**
     * Display a listing of cameras.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Camera::with(['location', 'category']);

        // Support both area_id and location_id
        if ($request->has('area_id')) {
            $query->where('location_id', $request->input('area_id'));
        } elseif ($request->has('location_id')) {
            $query->where('location_id', $request->input('location_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $cameras = $query->get();

        return $this->successResponse(CameraResource::collection($cameras), 'Daftar kamera berhasil diambil.');
    }

    /**
     * Display the specified camera details.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $camera = Camera::with(['location', 'category'])->findOrFail($id);

        return $this->successResponse(new CameraResource($camera), 'Detail kamera berhasil diambil.');
    }

    /**
     * Return secure streaming proxy endpoints for the mobile client.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function streamInfo($id)
    {
        $camera = Camera::findOrFail($id);

        return $this->successResponse([
            'camera_id' => $camera->id,
            'camera_name' => $camera->name,
            'status' => $camera->status instanceof \BackedEnum ? $camera->status->value : $camera->status,
            'webrtc_url' => url("/api/kamera/{$camera->id}/webrtc"),
            'stream_url' => url("/api/kamera/{$camera->id}/stream"),
        ], 'Informasi stream berhasil diambil.');
    }

    /**
     * Proxy WebRTC signaling requests to the go2rtc server.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function webrtcProxy(Request $request, $id)
    {
        $camera = Camera::findOrFail($id);

        $host = config('cctv.go2rtc_host', 'localhost');
        $port = config('cctv.go2rtc_port', 1984);
        
        try {
            $response = Http::withBody($request->getContent(), $request->header('Content-Type'))
                ->timeout(10)
                ->post("http://{$host}:{$port}/api/webrtc?src=" . urlencode($camera->stream_key));

            return response($response->body(), $response->status())
                ->header('Content-Type', $response->header('Content-Type'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal terhubung ke media server: ' . $e->getMessage()
            ], 502);
        }
    }

    /**
     * Proxy stream HTML interface from go2rtc server.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function streamProxy($id)
    {
        $camera = Camera::findOrFail($id);

        $host = config('cctv.go2rtc_host', 'localhost');
        $port = config('cctv.go2rtc_port', 1984);

        try {
            $response = Http::timeout(10)->get("http://{$host}:{$port}/stream.html", [
                'src' => $camera->stream_key
            ]);

            if (!$response->successful()) {
                return response('Stream HTML tidak tersedia di server go2rtc', 502);
            }

            // Return the html safely proxying through Laravel
            return response($response->body())
                ->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            return response('Koneksi media server gagal: ' . $e->getMessage(), 502);
        }
    }

    /**
     * Retrieve status summary of all cameras.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function statusSummary()
    {
        $locations = Location::with(['cameras'])->get();
        
        $perArea = $locations->map(function ($loc) {
            return [
                'area_id' => $loc->id,
                'area_name' => $loc->name,
                'total' => $loc->cameras->count(),
                'online' => $loc->cameras->where('status', CameraStatus::Online)->count(),
                'offline' => $loc->cameras->where('status', CameraStatus::Offline)->count(),
                'maintenance' => $loc->cameras->where('status', CameraStatus::Maintenance)->count(),
            ];
        });

        $overall = [
            'total' => Camera::count(),
            'online' => Camera::where('status', CameraStatus::Online)->count(),
            'offline' => Camera::where('status', CameraStatus::Offline)->count(),
            'maintenance' => Camera::where('status', CameraStatus::Maintenance)->count(),
        ];

        return $this->successResponse([
            'overall' => $overall,
            'per_area' => $perArea
        ], 'Ringkasan status kamera berhasil diambil.');
    }

    /**
     * Update camera status manually.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $camera = Camera::findOrFail($id);
        $oldStatus = $camera->status;

        $request->validate([
            'status' => 'required|string|in:online,offline,maintenance',
        ]);

        $newStatus = CameraStatus::from($request->status);

        if ($oldStatus !== $newStatus) {
            $camera->status = $newStatus;
            
            if ($newStatus === CameraStatus::Online) {
                $camera->last_online_at = now();
            } elseif ($newStatus === CameraStatus::Offline) {
                $camera->last_offline_at = now();
            }
            
            $camera->save();

            // Create event log
            MonitoringLog::create([
                'camera_id' => $camera->id,
                'event_type' => $newStatus->value,
                'description' => "Status kamera diubah manual oleh API.",
                'metadata' => [
                    'source' => 'api_patch',
                    'user' => $request->user()?->email,
                    'previous_status' => $oldStatus instanceof \BackedEnum ? $oldStatus->value : $oldStatus
                ],
                'recorded_at' => now(),
            ]);

            // Dispatch appropriate events
            if ($newStatus === CameraStatus::Online) {
                event(new CameraWentOnline($camera));
            } elseif ($newStatus === CameraStatus::Offline) {
                event(new CameraWentOffline($camera));
            }
        }

        return $this->successResponse(new CameraResource($camera), 'Status kamera berhasil diperbarui.');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Resources\LocationResource;
use App\Models\Client;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;

/**
 * Class ClientController
 * 
 * Handles Client API endpoints for the mobile application.
 * 
 * @package App\Http\Controllers\Api
 */
class ClientController extends Controller
{
    use ApiResponder;

    /**
     * Display a listing of clients with search and pagination.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Client::withCount(['locations', 'invoices', 'quotations']);

        if ($request->has('search') && $request->input('search') !== '') {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $paginator = $query->paginate($perPage);

        return $this->successResponse(
            ClientResource::collection($paginator->items()),
            'Daftar klien berhasil diambil.',
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
     * Display details of a specific client.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $client = Client::withCount(['locations', 'invoices', 'quotations'])->findOrFail($id);

        return $this->successResponse(new ClientResource($client), 'Detail klien berhasil diambil.');
    }

    /**
     * Display locations associated with a specific client.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function locations($id)
    {
        $client = Client::findOrFail($id);
        $locations = $client->locations()->withCount('cameras')->get();

        return $this->successResponse(LocationResource::collection($locations), 'Daftar lokasi klien berhasil diambil.');
    }
}

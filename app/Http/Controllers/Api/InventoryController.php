<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;

/**
 * Class InventoryController
 * 
 * Handles inventory (stok peralatan CCTV) endpoints for mobile client.
 * 
 * @package App\Http\Controllers\Api
 */
class InventoryController extends Controller
{
    use ApiResponder;

    /**
     * Display a listing of inventory items with search and category filters.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Inventory::with('location');

        if ($request->has('search') && $request->input('search') !== '') {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->input('category') !== '') {
            $query->where('category', $request->input('category'));
        }

        $perPage = $request->input('per_page', 15);
        $paginator = $query->paginate($perPage);

        return $this->successResponse(
            InventoryResource::collection($paginator->items()),
            'Daftar inventaris berhasil diambil.',
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
     * Display the specified inventory item.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = Inventory::with('location')->findOrFail($id);

        return $this->successResponse(new InventoryResource($item), 'Detail barang berhasil diambil.');
    }

    /**
     * Display inventory items that have stock below their minimum threshold.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function lowStock()
    {
        // Items where stock < min_stock
        $items = Inventory::with('location')
            ->whereColumn('stock', '<', 'min_stock')
            ->get();

        return $this->successResponse(InventoryResource::collection($items), 'Daftar barang dengan stok menipis berhasil diambil.');
    }
}

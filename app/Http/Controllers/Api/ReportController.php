<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Enums\CameraStatus;
use App\Enums\InvoiceStatus;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class ReportController
 * 
 * Handles report generation endpoints for the mobile client.
 * 
 * @package App\Http\Controllers\Api
 */
class ReportController extends Controller
{
    use ApiResponder;

    /**
     * Get camera uptime and health status report.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function cameraUptime()
    {
        $total = Camera::count();
        $online = Camera::where('status', CameraStatus::Online)->count();
        $offline = Camera::where('status', CameraStatus::Offline)->count();
        $maintenance = Camera::where('status', CameraStatus::Maintenance)->count();

        $uptimePercentage = $total > 0 ? round(($online / $total) * 100, 2) : 100;

        // Fetch cameras that went offline recently
        $offlineCameras = Camera::with('location')
            ->where('status', CameraStatus::Offline)
            ->orderBy('last_offline_at', 'desc')
            ->get();

        return $this->successResponse([
            'uptime_percentage' => $uptimePercentage,
            'summary' => [
                'total' => $total,
                'online' => $online,
                'offline' => $offline,
                'maintenance' => $maintenance,
            ],
            'offline_cameras' => $offlineCameras->map(function ($cam) {
                return [
                    'id' => $cam->id,
                    'name' => $cam->name,
                    'location' => $cam->location ? $cam->location->name : null,
                    'last_offline_at' => $cam->last_offline_at,
                ];
            }),
        ], 'Laporan uptime kamera berhasil dibuat.');
    }

    /**
     * Get financial reports containing monthly invoice revenues and outstanding totals.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function financial(Request $request)
    {
        // 1. Overall stats (all-time)
        $invoices = Invoice::where('status', '!=', InvoiceStatus::Cancelled)->get();
        
        $totalBilling = $invoices->sum('total');
        $totalPaid = $invoices->where('status', InvoiceStatus::Paid)->sum('total');
        $totalOutstanding = $invoices->whereIn('status', [InvoiceStatus::Sent, InvoiceStatus::Overdue, InvoiceStatus::Draft])->sum('total');

        // 2. Monthly Revenue breakdown (for charting in mobile app)
        // Group by year and month
        $monthlyRevenue = Invoice::select(
            DB::raw("DATE_FORMAT(issue_date, '%Y-%m') as month"),
            DB::raw("SUM(CASE WHEN status = 'paid' THEN total ELSE 0 END) as revenue"),
            DB::raw("SUM(CASE WHEN status != 'cancelled' AND status != 'paid' THEN total ELSE 0 END) as outstanding")
        )
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->limit(12)
        ->get();

        return $this->successResponse([
            'overall' => [
                'total_billing' => (float) $totalBilling,
                'total_paid' => (float) $totalPaid,
                'total_outstanding' => (float) $totalOutstanding,
                'count_total' => $invoices->count(),
                'count_paid' => $invoices->where('status', InvoiceStatus::Paid)->count(),
                'count_outstanding' => $invoices->whereIn('status', [InvoiceStatus::Sent, InvoiceStatus::Overdue, InvoiceStatus::Draft])->count(),
            ],
            'monthly_breakdown' => $monthlyRevenue->map(function ($row) {
                return [
                    'month_label' => \Carbon\Carbon::parse($row->month . '-01')->format('F Y'),
                    'month_key' => $row->month,
                    'revenue' => (float) $row->revenue,
                    'outstanding' => (float) $row->outstanding,
                ];
            }),
        ], 'Laporan keuangan berhasil dibuat.');
    }

    /**
     * Get inventory assets value and condition report.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function inventory()
    {
        $items = Inventory::all();

        $totalItems = $items->count();
        $totalStock = $items->sum('stock');
        
        // Value calculations
        $assetValue = $items->sum(function ($item) {
            return $item->purchase_price * $item->stock;
        });
        
        $retailValue = $items->sum(function ($item) {
            return $item->selling_price * $item->stock;
        });

        $expectedProfit = $retailValue - $assetValue;

        // Group by category
        $categoriesBreakdown = $items->groupBy('category')->map(function ($group, $cat) {
            return [
                'category' => $cat,
                'items_count' => $group->count(),
                'total_stock' => $group->sum('stock'),
                'asset_value' => (float) $group->sum(fn($i) => $i->purchase_price * $i->stock),
            ];
        })->values();

        // Count conditions
        $conditions = [
            'new' => $items->where('condition', \App\Enums\InventoryCondition::New ?? 'new')->sum('stock'),
            'used' => $items->where('condition', \App\Enums\InventoryCondition::Used ?? 'used')->sum('stock'),
            'damaged' => $items->where('condition', \App\Enums\InventoryCondition::Damaged ?? 'damaged')->sum('stock'),
        ];

        return $this->successResponse([
            'summary' => [
                'total_sku' => $totalItems,
                'total_stock' => $totalStock,
                'total_asset_value' => (float) $assetValue,
                'total_retail_value' => (float) $retailValue,
                'expected_profit' => (float) $expectedProfit,
            ],
            'categories_breakdown' => $categoriesBreakdown,
            'conditions' => $conditions,
        ], 'Laporan inventaris berhasil dibuat.');
    }
}

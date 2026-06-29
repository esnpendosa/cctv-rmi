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

/**
 * Class DashboardController
 * 
 * Handles summary aggregation for the mobile dashboard.
 * 
 * @package App\Http\Controllers\Api
 */
class DashboardController extends Controller
{
    use ApiResponder;

    /**
     * Get aggregated summary stats for the dashboard.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $totalKamera = Camera::count();
        $kameraOnline = Camera::where('status', CameraStatus::Online)->count();
        
        // Active alerts: Cameras offline in the last 1 hour
        $oneHourAgo = now()->subHour();
        $alertAktif = Camera::where('status', CameraStatus::Offline)
            ->where(function ($query) use ($oneHourAgo) {
                $query->where('last_offline_at', '>=', $oneHourAgo)
                      ->orWhereNull('last_offline_at');
            })
            ->count();

        // Total inventory value (Sum of purchase_price * stock)
        $inventories = Inventory::all();
        $nilaiInventarisTotal = $inventories->sum(function ($item) {
            return $item->purchase_price * $item->stock;
        });

        // Revenue this month (Sum of total of paid invoices issued this month)
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $revenueBulanIni = Invoice::whereBetween('issue_date', [$startOfMonth, $endOfMonth])
            ->where('status', InvoiceStatus::Paid)
            ->sum('total');

        return $this->successResponse([
            'total_kamera' => $totalKamera,
            'kamera_online' => $kameraOnline,
            'alert_aktif' => $alertAktif,
            'nilai_inventaris_total' => (float) $nilaiInventarisTotal,
            'revenue_bulan_ini' => (float) $revenueBulanIni,
            'month' => now()->format('F Y'),
        ], 'Ringkasan dashboard berhasil diambil.');
    }
}

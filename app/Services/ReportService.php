<?php

namespace App\Services;

use App\Enums\CameraStatus;
use App\Enums\InvoiceStatus;
use App\Models\AuditLog;
use App\Models\Camera;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\MonitoringLog;
use App\Repositories\Interfaces\CameraRepositoryInterface;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class ReportService
 * 
 * Handles generation of business and monitoring reports.
 * 
 * @package App\Services
 */
class ReportService
{
    /**
     * @var CameraRepositoryInterface
     */
    protected CameraRepositoryInterface $cameraRepository;

    /**
     * @var InventoryRepositoryInterface
     */
    protected InventoryRepositoryInterface $inventoryRepository;

    /**
     * ReportService constructor.
     * 
     * @param CameraRepositoryInterface $cameraRepository
     * @param InventoryRepositoryInterface $inventoryRepository
     */
    public function __construct(
        CameraRepositoryInterface $cameraRepository,
        InventoryRepositoryInterface $inventoryRepository
    ) {
        $this->cameraRepository = $cameraRepository;
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * Generate CCTV monitoring report data.
     * 
     * @param array $filters [start_date, end_date, camera_id, location_id, category_id]
     * @return array
     */
    public function getMonitoringReport(array $filters = []): array
    {
        $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date']) : Carbon::now()->subDays(7);
        $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date']) : Carbon::now();

        // Query timeline logs
        $logQuery = MonitoringLog::with(['camera.location', 'camera.category'])
            ->whereBetween('recorded_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        if (!empty($filters['camera_id'])) {
            $logQuery->where('camera_id', $filters['camera_id']);
        }

        if (!empty($filters['location_id'])) {
            $logQuery->whereHas('camera', function ($q) use ($filters) {
                $q->where('location_id', $filters['location_id']);
            });
        }

        if (!empty($filters['category_id'])) {
            $logQuery->whereHas('camera', function ($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            });
        }

        $logs = $logQuery->orderBy('recorded_at', 'desc')->get();

        // Calculate uptime percentage per camera
        $cameraQuery = Camera::where('is_active', true);
        if (!empty($filters['camera_id'])) {
            $cameraQuery->where('id', $filters['camera_id']);
        }
        if (!empty($filters['location_id'])) {
            $cameraQuery->where('location_id', $filters['location_id']);
        }
        if (!empty($filters['category_id'])) {
            $cameraQuery->where('category_id', $filters['category_id']);
        }

        $cameras = $cameraQuery->get();
        $uptimeStats = [];
        $daysDiff = $startDate->diffInDays($endDate) ?: 1;

        foreach ($cameras as $camera) {
            $uptimeStats[] = [
                'camera' => $camera,
                'uptime' => $this->cameraRepository->getUptimePercentage($camera, (int) $daysDiff),
            ];
        }

        return [
            'logs' => $logs,
            'uptime_stats' => $uptimeStats,
            'filters' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ]
        ];
    }

    /**
     * Generate financial revenue report data.
     * 
     * @param array $filters [start_date, end_date, client_id]
     * @return array
     */
    public function getRevenueReport(array $filters = []): array
    {
        $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date']) : Carbon::now()->startOfYear();
        $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date']) : Carbon::now();

        // Base query for invoices
        $invoiceQuery = Invoice::whereBetween('issue_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if (!empty($filters['client_id'])) {
            $invoiceQuery->where('client_id', $filters['client_id']);
        }

        $invoices = $invoiceQuery->get();

        // Summaries
        $summary = [
            'total_issued' => $invoices->sum('total'),
            'total_paid' => $invoices->where('status', InvoiceStatus::Paid)->sum('total'),
            'total_overdue' => $invoices->where('status', InvoiceStatus::Overdue)->sum('total'),
            'total_cancelled' => $invoices->where('status', InvoiceStatus::Cancelled)->sum('total'),
            'count_issued' => $invoices->count(),
            'count_paid' => $invoices->where('status', InvoiceStatus::Paid)->count(),
            'count_overdue' => $invoices->where('status', InvoiceStatus::Overdue)->count(),
        ];

        // Monthly chart data (e.g. for the current year or filter range)
        $monthlyData = [];
        $currentMonth = $startDate->copy()->startOfMonth();
        
        while ($currentMonth->lte($endDate)) {
            $monthStart = $currentMonth->toDateString();
            $monthEnd = $currentMonth->copy()->endOfMonth()->toDateString();
            
            $monthInvoices = Invoice::whereBetween('issue_date', [$monthStart, $monthEnd]);
            if (!empty($filters['client_id'])) {
                $monthInvoices->where('client_id', $filters['client_id']);
            }
            
            $monthInvoicesList = $monthInvoices->get();

            $monthlyData[] = [
                'label' => $currentMonth->format('M Y'),
                'issued' => $monthInvoicesList->sum('total'),
                'paid' => $monthInvoicesList->where('status', InvoiceStatus::Paid)->sum('total'),
            ];

            $currentMonth->addMonth();
        }

        return [
            'summary' => $summary,
            'monthly_chart' => $monthlyData,
            'invoices' => $invoices,
        ];
    }

    /**
     * Generate inventory status and movements report.
     * 
     * @return array
     */
    public function getInventoryReport(): array
    {
        $items = Inventory::with(['location'])->get();

        // Calculate valuation
        $valuation = [
            'purchase' => $items->sum(fn($i) => $i->stock * $i->purchase_price),
            'selling' => $items->sum(fn($i) => $i->stock * $i->selling_price),
        ];

        // Fetch low stock items
        $lowStock = $this->inventoryRepository->getLowStockItems();

        // Fetch stock movement history (via AuditLog where model_type = Inventory)
        $movements = AuditLog::with('user')
            ->where('model_type', Inventory::class)
            ->whereIn('action', ['stock_in', 'stock_out'])
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($log) {
                // Parse item name from model ID if needed, or get item details
                $inventory = Inventory::find($log->model_id);
                $sku = $inventory ? $inventory->sku : 'N/A';
                $name = $inventory ? $inventory->name : 'Item Terhapus';

                return [
                    'timestamp' => $log->created_at,
                    'sku' => $sku,
                    'item_name' => $name,
                    'user' => $log->user ? $log->user->name : 'System',
                    'action' => $log->action === 'stock_in' ? 'Masuk' : 'Keluar',
                    'quantity' => $log->new_values['quantity_changed'] ?? 0,
                    'reason' => $log->new_values['reason'] ?? '',
                    'notes' => $log->new_values['notes'] ?? '',
                    'old_stock' => $log->old_values['stock'] ?? 0,
                    'new_stock' => $log->new_values['stock'] ?? 0,
                ];
            });

        return [
            'valuation' => $valuation,
            'low_stock_items' => $lowStock,
            'movements' => $movements,
            'items' => $items,
        ];
    }

    /**
     * Generate client activity report data.
     * 
     * @return Collection
     */
    public function getClientReport(): Collection
    {
        return Client::withCount(['quotations', 'invoices'])
            ->get()
            ->map(function ($client) {
                $clientInvoices = Invoice::where('client_id', $client->id)->get();
                
                $client->total_revenue = $clientInvoices->sum('total');
                $client->paid_revenue = $clientInvoices->where('status', InvoiceStatus::Paid)->sum('total');
                
                return $client;
            });
    }
}

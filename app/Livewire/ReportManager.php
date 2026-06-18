<?php

namespace App\Livewire;

use App\Models\Camera;
use App\Models\Client;
use App\Models\Location;
use App\Models\CameraCategory;
use App\Services\ReportService;
use Livewire\Component;

/**
 * Class ReportManager
 * 
 * Handles report configuration, filtering, and tabbed displays of operational/financial data.
 * 
 * @package App\Livewire
 */
class ReportManager extends Component
{
    public string $activeTab = 'monitoring'; // monitoring, revenue, inventory, clients

    // Monitoring filters
    public string $m_start_date = '';
    public string $m_end_date = '';
    public ?int $m_camera_id = null;
    public ?int $m_location_id = null;
    public ?int $m_category_id = null;

    // Revenue filters
    public string $r_start_date = '';
    public string $r_end_date = '';
    public ?int $r_client_id = null;

    /**
     * Mount lifecycle hook to initialize filters.
     */
    public function mount(): void
    {
        $this->m_start_date = now()->subDays(7)->format('Y-m-d');
        $this->m_end_date = now()->format('Y-m-d');
        $this->r_start_date = now()->startOfYear()->format('Y-m-d');
        $this->r_end_date = now()->format('Y-m-d');
    }

    /**
     * Change active tab.
     * 
     * @param string $tab
     */
    public function changeTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    /**
     * Render the component view.
     * 
     * @param ReportService $reportService
     */
    public function render(ReportService $reportService)
    {
        // 1. Get Monitoring Report
        $monitoringData = $reportService->getMonitoringReport([
            'start_date' => $this->m_start_date,
            'end_date' => $this->m_end_date,
            'camera_id' => $this->m_camera_id,
            'location_id' => $this->m_location_id,
            'category_id' => $this->m_category_id,
        ]);

        // 2. Get Revenue Report
        $revenueData = $reportService->getRevenueReport([
            'start_date' => $this->r_start_date,
            'end_date' => $this->r_end_date,
            'client_id' => $this->r_client_id,
        ]);

        // 3. Get Inventory Report
        $inventoryData = $reportService->getInventoryReport();

        // 4. Get Client Report
        $clientReport = $reportService->getClientReport();

        // Feed lists for select filters
        $cameras = Camera::all();
        $locations = Location::all();
        $categories = CameraCategory::all();
        $clients = Client::all();

        return view('livewire.report-manager', [
            'monitoringLogs' => $monitoringData['logs'],
            'uptimeStats' => $monitoringData['uptime_stats'],
            'revenueSummary' => $revenueData['summary'],
            'monthlyChart' => $revenueData['monthly_chart'],
            'revenueInvoices' => $revenueData['invoices'],
            'inventoryValuation' => $inventoryData['valuation'],
            'lowStockItems' => $inventoryData['low_stock_items'],
            'stockMovements' => $inventoryData['movements'],
            'clientReport' => $clientReport,
            'cameras' => $cameras,
            'locations' => $locations,
            'categories' => $categories,
            'clients' => $clients,
        ])->layout('layouts.app');
    }
}

<?php

use App\Models\Camera;
use App\Http\Resources\CameraResource;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CameraController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\MonitoringController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Keep existing route for web compatibility
Route::get('/cameras/status', function () {
    return CameraResource::collection(Camera::with('location')->get());
});

// Authentication routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Authenticated API Routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Live endpoint uses the custom api_monitoring_live rate limiter (120 req/min)
    Route::middleware('throttle:api_monitoring_live')->get('/monitoring/live', [MonitoringController::class, 'live']);

    // All other routes use the standard api_standard rate limiter (60 req/min)
    Route::middleware('throttle:api_standard')->group(function () {
        // Auth actions
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        
        // Camera endpoints
        Route::get('/kamera', [CameraController::class, 'index']);
        Route::get('/kamera/status-summary', [CameraController::class, 'statusSummary']);
        Route::get('/kamera/{id}', [CameraController::class, 'show']);
        Route::get('/kamera/{id}/stream-info', [CameraController::class, 'streamInfo']);
        Route::get('/kamera/{id}/stream', [CameraController::class, 'streamProxy']);
        Route::post('/kamera/{id}/webrtc', [CameraController::class, 'webrtcProxy']);
        Route::patch('/kamera/{id}/status', [CameraController::class, 'updateStatus']);
        
        // Area/Location endpoints
        Route::get('/area', [AreaController::class, 'index']);
        Route::get('/area/{id}/kamera', [AreaController::class, 'cameras']);
        
        // Client endpoints
        Route::get('/klien', [ClientController::class, 'index']);
        Route::get('/klien/{id}', [ClientController::class, 'show']);
        Route::get('/klien/{id}/lokasi', [ClientController::class, 'locations']);
        
        // Monitoring endpoints
        Route::get('/monitoring/history/{kamera_id}', [MonitoringController::class, 'history']);
        Route::get('/monitoring/alert', [MonitoringController::class, 'alert']);
        
        // Inventory endpoints
        Route::get('/inventaris', [InventoryController::class, 'index']);
        Route::get('/inventaris/stok-menipis', [InventoryController::class, 'lowStock']);
        Route::get('/inventaris/{id}', [InventoryController::class, 'show']);
        
        // Finance endpoints
        Route::get('/invoice', [FinanceController::class, 'invoices']);
        Route::get('/invoice/statistik', [FinanceController::class, 'invoiceStatistics']);
        Route::get('/invoice/{id}', [FinanceController::class, 'invoiceDetails']);
        Route::get('/quotation', [FinanceController::class, 'quotations']);
        Route::get('/quotation/{id}', [FinanceController::class, 'quotationDetails']);
        
        // Report endpoints
        Route::get('/laporan/kamera-uptime', [ReportController::class, 'cameraUptime']);
        Route::get('/laporan/keuangan', [ReportController::class, 'financial']);
        Route::get('/laporan/inventaris', [ReportController::class, 'inventory']);
        
        // Dashboard Summary endpoint
        Route::get('/dashboard', [DashboardController::class, 'index']);
    });
});

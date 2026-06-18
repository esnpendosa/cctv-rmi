<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\CameraManager;
use App\Livewire\ClientManager;
use App\Livewire\InventoryManager;
use App\Livewire\QuotationManager;
use App\Livewire\InvoiceManager;
use App\Livewire\ReportManager;
use App\Livewire\PublicCameraViewer;
use App\Livewire\MonitorWall;

use App\Livewire\SettingsManager;

// Public Monitor Wall Route on root URL
Route::get('/', MonitorWall::class)->name('monitor.wall');

// Redirect old monitor path to root
Route::get('/monitor', function () {
    return redirect()->route('monitor.wall');
});

// Public Share Route for CCTV Streams
Route::get('/cameras/public/{token}', PublicCameraViewer::class)->name('cameras.public');

// Authenticated Panel Routes
Route::middleware(['auth'])->group(function () {
    
    // Main Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    // Camera Management
    Route::get('/cameras', CameraManager::class)
        ->middleware('can:camera.view')
        ->name('cameras.index');
        
    // Client & Location Management
    Route::get('/clients', ClientManager::class)->name('clients.index');
    
    // Inventory Management
    Route::get('/inventories', InventoryManager::class)
        ->middleware('can:inventory.view')
        ->name('inventories.index');
        
    // Quotations
    Route::get('/quotations', QuotationManager::class)
        ->middleware('can:invoice.view')
        ->name('quotations.index');
    Route::get('/quotations/{id}/print', function ($id) {
        $quotation = \App\Models\Quotation::with(['client', 'items.inventory'])->findOrFail($id);
        return view('quotations.print', compact('quotation'));
    })->name('quotations.pdf');
        
    // Invoices
    Route::get('/invoices', InvoiceManager::class)
        ->middleware('can:invoice.view')
        ->name('invoices.index');
    Route::get('/invoices/{id}/print', function ($id) {
        $invoice = \App\Models\Invoice::with(['client', 'items.inventory'])->findOrFail($id);
        return view('invoices.print', compact('invoice'));
    })->name('invoices.pdf');
        
    // Reports & Analytics
    Route::get('/reports', ReportManager::class)->name('reports.index');

    // Settings & Configuration
    Route::get('/settings', SettingsManager::class)
        ->middleware('can:settings.view')
        ->name('settings.index');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

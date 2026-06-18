<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

use Illuminate\Support\Facades\Schedule;
use App\Services\QuotationService;

Schedule::command('cctv:check-health')->everyFiveMinutes();
Schedule::command('cctv:check-overdue')->daily();
Schedule::call(function (QuotationService $quotationService) {
    $quotationService->checkExpiredQuotations();
})->daily();

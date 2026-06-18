<?php

namespace App\Console\Commands;

use App\Services\InvoiceService;
use App\Services\NotificationService;
use App\Models\Invoice;
use App\Enums\InvoiceStatus;
use Illuminate\Console\Command;

/**
 * Class CheckOverdueInvoices
 * 
 * Command to check and flag overdue invoices.
 * 
 * @package App\Console\Commands
 */
class CheckOverdueInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cctv:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds sent or draft invoices past their due date, marks them overdue, and alerts finance';

    /**
     * Execute the console command.
     * 
     * @param InvoiceService $invoiceService
     * @param NotificationService $notificationService
     * @return int
     */
    public function handle(InvoiceService $invoiceService, NotificationService $notificationService): int
    {
        $this->info('Checking for overdue invoices...');
        
        // Fetch invoices that are about to transition to overdue
        $aboutToBeOverdue = Invoice::whereIn('status', [InvoiceStatus::Draft, InvoiceStatus::Sent])
            ->where('due_date', '<', now()->toDateString())
            ->get();

        $count = $invoiceService->checkOverdueInvoices();
        
        $this->info("Completed. {$count} invoices marked as overdue.");

        // Send notification for each newly overdue invoice
        foreach ($aboutToBeOverdue as $invoice) {
            // Reload invoice to get updated status
            $invoice->refresh();
            if ($invoice->status === InvoiceStatus::Overdue) {
                $notificationService->notifyInvoiceOverdue($invoice);
                $this->line("Dispatched notification for invoice {$invoice->number}");
            }
        }

        return 0;
    }
}

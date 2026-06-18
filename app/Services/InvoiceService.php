<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

/**
 * Class InvoiceService
 * 
 * Handles business logic for invoice management.
 * 
 * @package App\Services
 */
class InvoiceService
{
    /**
     * @var InvoiceRepositoryInterface
     */
    protected InvoiceRepositoryInterface $invoiceRepository;

    /**
     * @var InventoryService
     */
    protected InventoryService $inventoryService;

    /**
     * InvoiceService constructor.
     * 
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param InventoryService $inventoryService
     */
    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        InventoryService $inventoryService
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Record a payment for an invoice and mark it as Paid.
     * Deducts stock if the invoice is not linked to a quotation.
     * 
     * @param Invoice $invoice
     * @param array $paymentData
     * @return Invoice
     */
    public function recordPayment(Invoice $invoice, array $paymentData): Invoice
    {
        return DB::transaction(function () use ($invoice, $paymentData) {
            $oldStatus = $invoice->status;

            $invoice->status = InvoiceStatus::Paid;
            $invoice->paid_at = now();
            $invoice->payment_method = $paymentData['payment_method'];
            if (isset($paymentData['payment_proof'])) {
                $invoice->payment_proof = $paymentData['payment_proof'];
            }
            $invoice->save();

            // Auto-deduct stock ONLY if the invoice has NO quotation_id (to avoid double-deduct)
            if ($oldStatus !== InvoiceStatus::Paid && empty($invoice->quotation_id)) {
                $invoice->load('items');
                foreach ($invoice->items as $item) {
                    if ($item->inventory_id) {
                        $this->inventoryService->adjustStock(
                            $item->inventory_id,
                            -$item->qty,
                            "Potong Stok (Invoice Lunas: {$invoice->number})",
                            "Dipotong otomatis oleh sistem saat invoice dibayar langsung."
                        );
                    }
                }
            }

            return $invoice;
        });
    }

    /**
     * Check and mark overdue invoices.
     * 
     * @return int Number of invoices marked as overdue
     */
    public function checkOverdueInvoices(): int
    {
        $overdueCount = 0;

        DB::transaction(function () use (&$overdueCount) {
            $invoices = Invoice::whereIn('status', [InvoiceStatus::Draft, InvoiceStatus::Sent])
                ->where('due_date', '<', now()->toDateString())
                ->get();

            foreach ($invoices as $invoice) {
                $invoice->status = InvoiceStatus::Overdue;
                $invoice->save();
                $overdueCount++;
            }
        });

        return $overdueCount;
    }
}

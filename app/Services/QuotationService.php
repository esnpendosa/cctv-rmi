<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\QuotationStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

/**
 * Class QuotationService
 * 
 * Handles business logic for client price quotations.
 * 
 * @package App\Services
 */
class QuotationService
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
     * QuotationService constructor.
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
     * Update quotation status. If accepted, deduct inventory stock.
     * 
     * @param Quotation $quotation
     * @param QuotationStatus $status
     * @return Quotation
     */
    public function updateStatus(Quotation $quotation, QuotationStatus $status): Quotation
    {
        DB::transaction(function () use ($quotation, $status) {
            $oldStatus = $quotation->status;
            $quotation->status = $status;
            $quotation->save();

            // Auto-deduct stock when quotation is accepted
            if ($status === QuotationStatus::Accepted && $oldStatus !== QuotationStatus::Accepted) {
                $this->deductStock($quotation);
            }
        });

        return $quotation;
    }

    /**
     * Deduct stock for all items in the quotation.
     * 
     * @param Quotation $quotation
     * @return void
     */
    protected function deductStock(Quotation $quotation): void
    {
        $quotation->load('items.inventory');
        foreach ($quotation->items as $item) {
            if ($item->inventory_id) {
                $this->inventoryService->adjustStock(
                    $item->inventory_id,
                    -$item->qty,
                    "Potong Stok (Penawaran Diterima: {$quotation->number})",
                    "Dipotong otomatis oleh sistem saat penawaran diterima."
                );
            }
        }
    }

    /**
     * Accept a quotation by ID.
     * 
     * @param int $id
     * @return Invoice
     */
    public function acceptQuotation(int $id): Invoice
    {
        $quotation = Quotation::findOrFail($id);
        return $this->convertToInvoice($quotation, auth()->id() ?? 1);
    }

    /**
     * Convert a Quotation to a Draft Invoice.
     * 
     * @param Quotation $quotation
     * @param int $userId The user who initiates the conversion
     * @return Invoice
     */
    public function convertToInvoice(Quotation $quotation, int $userId): Invoice
    {
        return DB::transaction(function () use ($quotation, $userId) {
            // Generate next invoice number
            $invoiceNumber = $this->invoiceRepository->generateInvoiceNumber();

            // Create the invoice
            $invoice = Invoice::create([
                'number' => $invoiceNumber,
                'quotation_id' => $quotation->id,
                'client_id' => $quotation->client_id,
                'status' => InvoiceStatus::Draft,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(config('cctv.invoice_due_days', 30))->toDateString(),
                'notes' => $quotation->notes,
                'subtotal' => $quotation->subtotal,
                'discount_amount' => $quotation->discount_amount,
                'tax_percent' => $quotation->tax_percent,
                'tax_amount' => $quotation->tax_amount,
                'total' => $quotation->total,
                'created_by' => $userId,
            ]);

            // Copy items
            $quotation->load('items');
            foreach ($quotation->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'inventory_id' => $item->inventory_id,
                    'description' => $item->description,
                    'qty' => $item->qty,
                    'unit_price' => $item->unit_price,
                    'discount_percent' => $item->discount_percent,
                    'subtotal' => $item->subtotal,
                ]);
            }

            // Update quotation status to accepted if it wasn't already
            if ($quotation->status !== QuotationStatus::Accepted) {
                $this->updateStatus($quotation, QuotationStatus::Accepted);
            }

            return $invoice;
        });
    }

    /**
     * Check for expired quotations and mark them as expired.
     * 
     * @return int
     */
    public function checkExpiredQuotations(): int
    {
        return DB::transaction(function () {
            $expired = Quotation::whereIn('status', [
                QuotationStatus::Draft,
                QuotationStatus::Sent
            ])
            ->where('valid_until', '<', now()->toDateString())
            ->get();

            foreach ($expired as $quotation) {
                $quotation->status = QuotationStatus::Expired;
                $quotation->save();
            }

            return $expired->count();
        });
    }
}

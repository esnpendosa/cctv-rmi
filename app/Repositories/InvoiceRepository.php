<?php

namespace App\Repositories;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class InvoiceRepository
 * 
 * Implements invoice database operations using Eloquent.
 * 
 * @package App\Repositories
 */
class InvoiceRepository implements InvoiceRepositoryInterface
{
    /**
     * Get all invoices.
     * 
     * @return Collection
     */
    public function all(): Collection
    {
        return Invoice::with(['client', 'creator'])->orderBy('created_at', 'desc')->get();
    }

    /**
     * Find an invoice by ID.
     * 
     * @param int $id
     * @return Invoice|null
     */
    public function find(int $id): ?Invoice
    {
        return Invoice::with(['client', 'creator', 'items.inventory', 'quotation'])->find($id);
    }

    /**
     * Create a new invoice.
     * 
     * @param array $data
     * @return Invoice
     */
    public function create(array $data): Invoice
    {
        return Invoice::create($data);
    }

    /**
     * Update an invoice.
     * 
     * @param int $id
     * @param array $data
     * @return Invoice
     */
    public function update(int $id, array $data): Invoice
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update($data);
        return $invoice;
    }

    /**
     * Delete an invoice.
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $invoice = Invoice::find($id);
        if ($invoice) {
            return (bool) $invoice->delete();
        }
        return false;
    }

    /**
     * Generate the next unique invoice number (INV-YYYY-XXX).
     * 
     * @return string
     */
    public function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $prefix = config('cctv.invoice_prefix', 'INV');
        
        $lastInvoice = Invoice::whereYear('issue_date', $year)
            ->orderBy('number', 'desc')
            ->first();

        if (!$lastInvoice) {
            $nextNumber = 1;
        } else {
            // Expect prefix-YYYY-XXX
            $parts = explode('-', $lastInvoice->number);
            $lastSeq = count($parts) === 3 ? (int) $parts[2] : 0;
            $nextNumber = $lastSeq + 1;
        }

        return sprintf('%s-%s-%03d', $prefix, $year, $nextNumber);
    }

    /**
     * Get count of overdue invoices.
     * 
     * @return int
     */
    public function getOverdueCount(): int
    {
        return Invoice::where('status', InvoiceStatus::Overdue)->count();
    }

    /**
     * Get list of overdue invoices.
     * 
     * @return Collection
     */
    public function getOverdueInvoices(): Collection
    {
        return Invoice::with(['client'])
            ->where('status', InvoiceStatus::Overdue)
            ->get();
    }
}

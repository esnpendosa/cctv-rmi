<?php

namespace App\Repositories\Interfaces;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface InvoiceRepositoryInterface
 * 
 * Defines the contract for invoice persistence operations.
 * 
 * @package App\Repositories\Interfaces
 */
interface InvoiceRepositoryInterface
{
    /**
     * Get all invoices.
     * 
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Find an invoice by ID.
     * 
     * @param int $id
     * @return Invoice|null
     */
    public function find(int $id): ?Invoice;

    /**
     * Create a new invoice.
     * 
     * @param array $data
     * @return Invoice
     */
    public function create(array $data): Invoice;

    /**
     * Update an invoice.
     * 
     * @param int $id
     * @param array $data
     * @return Invoice
     */
    public function update(int $id, array $data): Invoice;

    /**
     * Delete an invoice.
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Generate the next unique invoice number (INV-YYYY-XXX).
     * 
     * @return string
     */
    public function generateInvoiceNumber(): string;

    /**
     * Get count of overdue invoices.
     * 
     * @return int
     */
    public function getOverdueCount(): int;

    /**
     * Get list of overdue invoices.
     * 
     * @return Collection
     */
    public function getOverdueInvoices(): Collection;
}

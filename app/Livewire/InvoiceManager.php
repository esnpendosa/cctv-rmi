<?php

namespace App\Livewire;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\InvoiceService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class InvoiceManager
 * 
 * Manages client billing invoices, payments, and status workflows.
 * 
 * @package App\Livewire
 */
class InvoiceManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $statusFilter = '';

    // Form states
    public bool $isFormOpen = false;
    public ?int $invoiceId = null;
    public ?int $client_id = null;
    public ?int $quotation_id = null;
    public string $issue_date = '';
    public string $due_date = '';
    public string $status = 'draft';
    public float $discount_amount = 0; // global discount amount
    public float $tax_percent = 12; // default 12% PPN as per database schema default
    public string $notes = '';

    // Dynamic items list
    public array $formItems = []; // [['inventory_id' => '', 'description' => '', 'qty' => 1, 'unit_price' => 0, 'discount_percent' => 0]]

    // Total preview values
    public float $subtotal = 0;
    public float $tax_amount = 0;
    public float $total = 0;

    // Payment state
    public bool $isPaymentModalOpen = false;
    public ?int $selectedInvoiceId = null;
    public string $payment_method = '';
    public string $payment_proof = '';

    /**
     * Reset page on filter changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Open form to create a new invoice.
     */
    public function create(): void
    {
        $this->resetForm();
        $this->issue_date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(30)->format('Y-m-d');
        $this->addFormItem();
        $this->isFormOpen = true;
    }

    /**
     * Open form to edit an existing invoice.
     * 
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $this->resetForm();
        $invoice = Invoice::with('items')->findOrFail($id);
        
        $this->invoiceId = $invoice->id;
        $this->client_id = $invoice->client_id;
        $this->quotation_id = $invoice->quotation_id;
        $this->issue_date = $invoice->issue_date->format('Y-m-d');
        $this->due_date = $invoice->due_date->format('Y-m-d');
        $this->status = $invoice->status->value;
        $this->discount_amount = $invoice->discount_amount;
        $this->tax_percent = $invoice->tax_percent;
        $this->notes = $invoice->notes ?? '';

        foreach ($invoice->items as $item) {
            $this->formItems[] = [
                'inventory_id' => $item->inventory_id,
                'description' => $item->description,
                'qty' => $item->qty,
                'unit_price' => $item->unit_price,
                'discount_percent' => $item->discount_percent,
            ];
        }

        $this->calculateTotals();
        $this->isFormOpen = true;
    }

    /**
     * Add an item row to the form.
     */
    public function addFormItem(): void
    {
        $this->formItems[] = [
            'inventory_id' => null,
            'description' => '',
            'qty' => 1,
            'unit_price' => 0,
            'discount_percent' => 0,
        ];
        $this->calculateTotals();
    }

    /**
     * Remove an item row from the form.
     * 
     * @param int $index
     * @return void
     */
    public function removeFormItem(int $index): void
    {
        unset($this->formItems[$index]);
        $this->formItems = array_values($this->formItems);
        if (empty($this->formItems)) {
            $this->addFormItem();
        }
        $this->calculateTotals();
    }

    /**
     * Handle change of item selection to auto-fill details.
     * 
     * @param int $index
     * @return void
     */
    public function updateItemPrice(int $index): void
    {
        $itemId = $this->formItems[$index]['inventory_id'];
        if ($itemId) {
            $inventory = Inventory::find($itemId);
            if ($inventory) {
                $this->formItems[$index]['unit_price'] = $inventory->selling_price;
                $this->formItems[$index]['description'] = $inventory->brand . ' ' . $inventory->model . ' - ' . $inventory->name;
            }
        }
        $this->calculateTotals();
    }

    /**
     * Calculate invoice subtotal, tax_amount, and total.
     */
    public function calculateTotals(): void
    {
        $this->subtotal = 0;
        foreach ($this->formItems as $item) {
            $qty = (int) ($item['qty'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            $discPercent = (float) ($item['discount_percent'] ?? 0);
            
            $itemSub = $qty * $price;
            $itemSub -= $itemSub * ($discPercent / 100);
            
            $this->subtotal += max(0, $itemSub);
        }

        $netBeforeTax = max(0, $this->subtotal - $this->discount_amount);
        $this->tax_amount = $netBeforeTax * ($this->tax_percent / 100);
        $this->total = $netBeforeTax + $this->tax_amount;
    }

    /**
     * Save invoice.
     */
    public function save(): void
    {
        $this->validate([
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|string',
            'discount_amount' => 'required|numeric|min:0',
            'tax_percent' => 'required|numeric|min:0|max:100',
            'formItems.*.description' => 'required|string|max:255',
            'formItems.*.qty' => 'required|integer|min:1',
            'formItems.*.unit_price' => 'required|numeric|min:0',
            'formItems.*.discount_percent' => 'required|numeric|min:0|max:100',
        ]);

        $this->calculateTotals();

        $data = [
            'client_id' => $this->client_id,
            'quotation_id' => $this->quotation_id,
            'status' => InvoiceStatus::from($this->status),
            'issue_date' => \Carbon\Carbon::parse($this->issue_date),
            'due_date' => \Carbon\Carbon::parse($this->due_date),
            'discount_amount' => $this->discount_amount,
            'tax_percent' => $this->tax_percent,
            'tax_amount' => $this->tax_amount,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'notes' => $this->notes,
        ];

        // Format items
        $itemsData = [];
        foreach ($this->formItems as $item) {
            $qty = $item['qty'];
            $price = $item['unit_price'];
            $discPercent = $item['discount_percent'];
            $itemSub = $qty * $price;
            $itemSub -= $itemSub * ($discPercent / 100);

            $itemsData[] = [
                'inventory_id' => $item['inventory_id'],
                'description' => $item['description'],
                'qty' => $qty,
                'unit_price' => $price,
                'discount_percent' => $discPercent,
                'subtotal' => max(0, $itemSub),
            ];
        }

        if ($this->invoiceId) {
            $invoice = Invoice::findOrFail($this->invoiceId);
            $invoice->update($data);
            
            $invoice->items()->delete();
            foreach ($itemsData as $it) {
                $invoice->items()->create($it);
            }

            session()->flash('success', 'Invoice berhasil diperbarui.');
        } else {
            // Generate number using the repository binding
            $repo = app(\App\Repositories\Interfaces\InvoiceRepositoryInterface::class);
            $data['number'] = $repo->generateInvoiceNumber();
            $data['created_by'] = auth()->id() ?? 1;

            $invoice = Invoice::create($data);
            foreach ($itemsData as $it) {
                $invoice->items()->create($it);
            }
            session()->flash('success', 'Invoice baru berhasil ditambahkan.');
        }

        $this->closeForm();
    }

    /**
     * Delete an invoice.
     * 
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->items()->delete();
        $invoice->delete();
        session()->flash('success', 'Invoice berhasil dihapus.');
    }

    /**
     * Open Payment Record modal.
     * 
     * @param int $id
     * @return void
     */
    public function openPaymentModal(int $id): void
    {
        $this->selectedInvoiceId = $id;
        $this->payment_method = 'Cash';
        $this->payment_proof = '';
        $this->isPaymentModalOpen = true;
    }

    /**
     * Close Payment Record modal.
     */
    public function closePaymentModal(): void
    {
        $this->isPaymentModalOpen = false;
        $this->selectedInvoiceId = null;
        $this->resetErrorBag();
    }

    /**
     * Save Payment Record.
     * 
     * @param InvoiceService $invoiceService
     * @return void
     */
    public function recordPayment(InvoiceService $invoiceService): void
    {
        $this->validate([
            'payment_method' => 'required|string|max:50',
            'payment_proof' => 'nullable|string|max:255',
        ]);

        if ($this->selectedInvoiceId) {
            $invoice = Invoice::findOrFail($this->selectedInvoiceId);
            $paymentData = [
                'payment_method' => $this->payment_method,
            ];
            if ($this->payment_proof) {
                $paymentData['payment_proof'] = $this->payment_proof;
            }

            $invoiceService->recordPayment($invoice, $paymentData);

            session()->flash('success', 'Pembayaran berhasil dicatat. Status invoice telah diperbarui dan stok disesuaikan jika diperlukan.');
            $this->closePaymentModal();
        }
    }

    /**
     * Close input form modal.
     */
    public function closeForm(): void
    {
        $this->isFormOpen = false;
        $this->resetForm();
    }

    /**
     * Reset form fields.
     */
    protected function resetForm(): void
    {
        $this->invoiceId = null;
        $this->client_id = null;
        $this->quotation_id = null;
        $this->issue_date = '';
        $this->due_date = '';
        $this->status = 'draft';
        $this->discount_amount = 0;
        $this->tax_percent = 12;
        $this->notes = '';
        $this->formItems = [];
        $this->subtotal = 0;
        $this->tax_amount = 0;
        $this->total = 0;
        $this->resetErrorBag();
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        $query = Invoice::with(['client', 'items.inventory']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('client', function($qc) {
                      $qc->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('company', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $invoices = $query->paginate(10);
        $clients = Client::all();
        $inventories = Inventory::all();

        return view('livewire.invoice-manager', [
            'invoices' => $invoices,
            'clients' => $clients,
            'inventories' => $inventories,
        ])->layout('layouts.app');
    }
}

<?php

namespace App\Livewire;

use App\Enums\QuotationStatus;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Services\QuotationService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class QuotationManager
 * 
 * Manages client project quotes, item additions, status updates, and invoice conversions.
 * 
 * @package App\Livewire
 */
class QuotationManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $statusFilter = '';

    // Form states
    public bool $isFormOpen = false;
    public ?int $quotationId = null;
    public ?int $client_id = null;
    public string $date = '';
    public string $expires_at = '';
    public string $status = 'draft';
    public float $discount = 0; // global discount
    public string $notes = '';

    // Dynamic items list
    public array $formItems = []; // [['inventory_id' => '', 'quantity' => 1, 'price' => 0, 'discount_percent' => 0, 'description' => '']]

    // Total preview values
    public float $subtotal = 0;
    public float $tax = 0;
    public float $total = 0;

    /**
     * Reset page on filter changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Open form to create a new quotation.
     */
    public function create(): void
    {
        $this->resetForm();
        $this->date = now()->format('Y-m-d');
        $this->expires_at = now()->addDays(30)->format('Y-m-d');
        $this->addFormItem();
        $this->isFormOpen = true;
    }

    /**
     * Open form to edit an existing quotation.
     * 
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $this->resetForm();
        $quote = Quotation::with('items')->findOrFail($id);
        
        $this->quotationId = $quote->id;
        $this->client_id = $quote->client_id;
        $this->date = $quote->created_at->format('Y-m-d');
        $this->expires_at = $quote->valid_until->format('Y-m-d');
        $this->status = $quote->status->value;
        $this->discount = $quote->discount_amount;
        $this->notes = $quote->notes ?? '';

        foreach ($quote->items as $item) {
            $this->formItems[] = [
                'inventory_id' => $item->inventory_id,
                'quantity' => $item->qty,
                'price' => $item->unit_price,
                'discount_percent' => $item->discount_percent,
                'description' => $item->description,
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
            'quantity' => 1,
            'price' => 0,
            'discount_percent' => 0,
            'description' => '',
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
     * Handle change of item selection to auto-fill price.
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
                $this->formItems[$index]['price'] = $inventory->selling_price;
                $this->formItems[$index]['description'] = $inventory->name;
            }
        }
        $this->calculateTotals();
    }

    /**
     * Calculate quotation subtotals, PPN tax, and totals in real-time.
     */
    public function calculateTotals(): void
    {
        $this->subtotal = 0;
        foreach ($this->formItems as $item) {
            $qty = (int) ($item['quantity'] ?? 0);
            $price = (float) ($item['price'] ?? 0);
            $discPercent = (float) ($item['discount_percent'] ?? 0);
            
            $itemSub = $qty * $price;
            $itemSub -= ($itemSub * ($discPercent / 100)); // Subtract item discount percent
            
            $this->subtotal += max(0, $itemSub);
        }

        $netBeforeTax = max(0, $this->subtotal - $this->discount);
        $taxPercent = config('cctv.default_tax_rate', 12);
        $this->tax = $netBeforeTax * ($taxPercent / 100);
        $this->total = $netBeforeTax + $this->tax;
    }

    /**
     * Save quotation.
     */
    public function save(QuotationService $quotationService): void
    {
        $this->validate([
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'expires_at' => 'required|date|after_or_equal:date',
            'status' => 'required|string',
            'discount' => 'required|numeric|min:0',
            'formItems.*.inventory_id' => 'required|exists:inventories,id',
            'formItems.*.quantity' => 'required|integer|min:1',
            'formItems.*.price' => 'required|numeric|min:0',
            'formItems.*.discount_percent' => 'required|numeric|min:0|max:100',
        ]);

        $this->calculateTotals();

        $data = [
            'client_id' => $this->client_id,
            'status' => QuotationStatus::from($this->status),
            'valid_until' => \Carbon\Carbon::parse($this->expires_at),
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount,
            'tax_percent' => config('cctv.default_tax_rate', 12),
            'tax_amount' => $this->tax,
            'total' => $this->total,
            'notes' => $this->notes,
            'created_by' => auth()->id() ?? 1,
        ];

        // Format items for service / repository
        $itemsData = [];
        foreach ($this->formItems as $item) {
            $qty = $item['quantity'];
            $price = $item['price'];
            $discPercent = $item['discount_percent'];
            $desc = $item['description'] ?: 'CCTV Item';

            $itemSub = $qty * $price;
            $itemSub -= ($itemSub * ($discPercent / 100));

            $itemsData[] = [
                'inventory_id' => $item['inventory_id'],
                'description' => $desc,
                'qty' => $qty,
                'unit_price' => $price,
                'discount_percent' => $discPercent,
                'subtotal' => max(0, $itemSub),
            ];
        }

        if ($this->quotationId) {
            $quotation = Quotation::findOrFail($this->quotationId);
            $quotation->update($data);
            
            // Delete old items and insert updated ones
            $quotation->items()->delete();
            foreach ($itemsData as $it) {
                $quotation->items()->create($it);
            }

            session()->flash('success', 'Penawaran berhasil diperbarui.');
        } else {
            // Generate number
            $prefix = config('cctv.quotation_prefix', 'QUO');
            $data['number'] = $prefix . '-' . date('Ymd') . '-' . sprintf('%04d', Quotation::count() + 1);
            $quote = Quotation::create($data);
            foreach ($itemsData as $it) {
                $quote->items()->create($it);
            }
            session()->flash('success', 'Penawaran baru berhasil ditambahkan.');
        }

        $this->closeForm();
    }

    /**
     * Accept quotation (convert to Invoice and update inventory).
     * 
     * @param int $id
     * @param QuotationService $quotationService
     * @return void
     */
    public function accept(int $id, QuotationService $quotationService): void
    {
        try {
            $quotationService->acceptQuotation($id);
            session()->flash('success', 'Penawaran disetujui! Faktur/Invoice baru telah dibuat dan stok inventaris disesuaikan.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    /**
     * Delete quotation.
     * 
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $quote = Quotation::findOrFail($id);
        $quote->items()->delete();
        $quote->delete();
        session()->flash('success', 'Penawaran berhasil dihapus.');
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
        $this->quotationId = null;
        $this->client_id = null;
        $this->subject = '';
        $this->date = '';
        $this->expires_at = '';
        $this->status = 'draft';
        $this->discount = 0;
        $this->notes = '';
        $this->formItems = [];
        $this->subtotal = 0;
        $this->tax = 0;
        $this->total = 0;
        $this->resetErrorBag();
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        $query = Quotation::with(['client', 'items.inventory']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('number', 'like', '%' . $this->search . '%')
                  ->orWhere('notes', 'like', '%' . $this->search . '%')
                  ->orWhereHas('client', function($qc) {
                      $qc->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('company', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $quotations = $query->paginate(10);
        $clients = Client::all();
        $inventories = Inventory::where('stock', '>', 0)->get();

        return view('livewire.quotation-manager', [
            'quotations' => $quotations,
            'clients' => $clients,
            'inventories' => $inventories,
        ])->layout('layouts.app');
    }
}

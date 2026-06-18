<?php

namespace App\Livewire;

use App\Enums\InventoryCondition;
use App\Models\Inventory;
use App\Models\Location;
use App\Services\InventoryService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class InventoryManager
 * 
 * Manages warehouse items, stock warnings, and audited stock adjustments.
 * 
 * @package App\Livewire
 */
class InventoryManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public string $search = '';
    public string $conditionFilter = '';
    public string $locationFilter = '';

    // Form states
    public bool $isFormOpen = false;
    public ?int $inventoryId = null;
    public string $sku = '';
    public string $name = '';
    public string $category = '';
    public string $brand = '';
    public string $model = '';
    public float $purchase_price = 0;
    public float $selling_price = 0;
    public int $stock = 0;
    public int $min_stock = 5;
    public string $unit = 'pcs';
    public string $condition = 'new';
    public ?int $location_id = null;
    public string $notes = '';

    // Adjustment state
    public bool $isAdjustOpen = false;
    public ?Inventory $adjustingItem = null;
    public int $adjustQuantity = 0; // positive for stock_in, negative for stock_out
    public string $adjustReason = '';
    public string $adjustNotes = '';

    /**
     * Set up validation rules.
     */
    protected function rules(): array
    {
        return [
            'sku' => 'required|string|max:50|unique:inventories,sku,' . ($this->inventoryId ?? 'NULL') . ',id',
            'name' => 'required|string|max:100',
            'category' => 'required|string|max:50',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'condition' => 'required|string',
            'location_id' => 'required|exists:locations,id',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Reset pagination when search changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Open form to create a new item.
     */
    public function create(): void
    {
        $this->resetForm();
        $this->isFormOpen = true;
    }

    /**
     * Open form to edit an item.
     * 
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $this->resetForm();
        $item = Inventory::findOrFail($id);
        
        $this->inventoryId = $item->id;
        $this->sku = $item->sku;
        $this->name = $item->name;
        $this->category = $item->category;
        $this->brand = $item->brand;
        $this->model = $item->model;
        $this->purchase_price = $item->purchase_price;
        $this->selling_price = $item->selling_price;
        $this->stock = $item->stock;
        $this->min_stock = $item->min_stock;
        $this->unit = $item->unit;
        $this->condition = $item->condition->value;
        $this->location_id = $item->location_id;
        $this->notes = $item->notes ?? '';

        $this->isFormOpen = true;
    }

    /**
     * Save inventory item.
     */
    public function save(): void
    {
        $this->validate();

        $data = [
            'sku' => $this->sku,
            'name' => $this->name,
            'category' => $this->category,
            'brand' => $this->brand,
            'model' => $this->model,
            'purchase_price' => $this->purchase_price,
            'selling_price' => $this->selling_price,
            'stock' => $this->stock,
            'min_stock' => $this->min_stock,
            'unit' => $this->unit,
            'condition' => InventoryCondition::from($this->condition),
            'location_id' => $this->location_id,
            'notes' => $this->notes,
        ];

        if ($this->inventoryId) {
            Inventory::findOrFail($this->inventoryId)->update($data);
            session()->flash('success', 'Barang inventaris berhasil diperbarui.');
        } else {
            Inventory::create($data);
            session()->flash('success', 'Barang inventaris baru berhasil ditambahkan.');
        }

        $this->closeForm();
    }

    /**
     * Delete an item.
     * 
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $item = Inventory::findOrFail($id);
        $item->delete();
        session()->flash('success', 'Barang inventaris berhasil dihapus.');
    }

    /**
     * Open stock adjustment modal.
     * 
     * @param int $id
     * @return void
     */
    public function openAdjust(int $id): void
    {
        $this->adjustingItem = Inventory::findOrFail($id);
        $this->adjustQuantity = 0;
        $this->adjustReason = '';
        $this->adjustNotes = '';
        $this->isAdjustOpen = true;
    }

    /**
     * Adjust stock using the service.
     * 
     * @param InventoryService $inventoryService
     * @return void
     */
    public function saveAdjustment(InventoryService $inventoryService): void
    {
        $this->validate([
            'adjustQuantity' => 'required|integer|not_in:0',
            'adjustReason' => 'required|string|max:255',
            'adjustNotes' => 'nullable|string',
        ]);

        if ($this->adjustingItem) {
            // Check if subtraction exceeds current stock
            if ($this->adjustQuantity < 0 && abs($this->adjustQuantity) > $this->adjustingItem->stock) {
                $this->addError('adjustQuantity', 'Pengurangan stok melebihi stok saat ini.');
                return;
            }

            $inventoryService->adjustStock(
                $this->adjustingItem->id,
                $this->adjustQuantity,
                $this->adjustReason,
                $this->adjustNotes
            );

            session()->flash('success', 'Stok barang berhasil disesuaikan.');
            $this->closeAdjust();
        }
    }

    /**
     * Close adjustment modal.
     */
    public function closeAdjust(): void
    {
        $this->isAdjustOpen = false;
        $this->adjustingItem = null;
        $this->resetErrorBag();
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
        $this->inventoryId = null;
        $this->sku = '';
        $this->name = '';
        $this->category = '';
        $this->brand = '';
        $this->model = '';
        $this->purchase_price = 0;
        $this->selling_price = 0;
        $this->stock = 0;
        $this->min_stock = 5;
        $this->unit = 'pcs';
        $this->condition = 'new';
        $this->location_id = null;
        $this->notes = '';
        $this->resetErrorBag();
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        $query = Inventory::with(['location']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%')
                  ->orWhere('brand', 'like', '%' . $this->search . '%')
                  ->orWhere('model', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->conditionFilter) {
            $query->where('condition', $this->conditionFilter);
        }

        if ($this->locationFilter) {
            $query->where('location_id', $this->locationFilter);
        }

        $items = $query->paginate(10);
        $locations = Location::all();

        return view('livewire.inventory-manager', [
            'items' => $items,
            'locations' => $locations,
        ])->layout('layouts.app');
    }
}

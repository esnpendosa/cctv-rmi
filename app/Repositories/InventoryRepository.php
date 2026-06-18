<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class InventoryRepository
 * 
 * Implements inventory database operations using Eloquent.
 * 
 * @package App\Repositories
 */
class InventoryRepository implements InventoryRepositoryInterface
{
    /**
     * Get all inventory items.
     * 
     * @return Collection
     */
    public function all(): Collection
    {
        return Inventory::with(['location'])->orderBy('name', 'asc')->get();
    }

    /**
     * Find an inventory item by ID.
     * 
     * @param int $id
     * @return Inventory|null
     */
    public function find(int $id): ?Inventory
    {
        return Inventory::with(['location'])->find($id);
    }

    /**
     * Create a new inventory item.
     * 
     * @param array $data
     * @return Inventory
     */
    public function create(array $data): Inventory
    {
        return Inventory::create($data);
    }

    /**
     * Update an inventory item.
     * 
     * @param int $id
     * @param array $data
     * @return Inventory
     */
    public function update(int $id, array $data): Inventory
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->update($data);
        return $inventory;
    }

    /**
     * Delete an inventory item.
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $inventory = Inventory::find($id);
        if ($inventory) {
            return (bool) $inventory->delete();
        }
        return false;
    }

    /**
     * Get count of items with stock <= min_stock.
     * 
     * @return int
     */
    public function getLowStockCount(): int
    {
        return Inventory::whereColumn('stock', '<=', 'min_stock')->count();
    }

    /**
     * Get all items with stock <= min_stock.
     * 
     * @return Collection
     */
    public function getLowStockItems(): Collection
    {
        return Inventory::with(['location'])
            ->whereColumn('stock', '<=', 'min_stock')
            ->get();
    }

    /**
     * Adjust stock level of an item.
     * 
     * @param int $id
     * @param int $quantity Change amount (can be positive or negative)
     * @return Inventory
     */
    public function adjustStock(int $id, int $quantity): Inventory
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->stock += $quantity;
        if ($inventory->stock < 0) {
            $inventory->stock = 0;
        }
        $inventory->save();
        return $inventory;
    }
}

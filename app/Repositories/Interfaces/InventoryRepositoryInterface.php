<?php

namespace App\Repositories\Interfaces;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface InventoryRepositoryInterface
 * 
 * Defines the contract for inventory persistence operations.
 * 
 * @package App\Repositories\Interfaces
 */
interface InventoryRepositoryInterface
{
    /**
     * Get all inventory items.
     * 
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Find an inventory item by ID.
     * 
     * @param int $id
     * @return Inventory|null
     */
    public function find(int $id): ?Inventory;

    /**
     * Create a new inventory item.
     * 
     * @param array $data
     * @return Inventory
     */
    public function create(array $data): Inventory;

    /**
     * Update an inventory item.
     * 
     * @param int $id
     * @param array $data
     * @return Inventory
     */
    public function update(int $id, array $data): Inventory;

    /**
     * Delete an inventory item.
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get count of items with stock <= min_stock.
     * 
     * @return int
     */
    public function getLowStockCount(): int;

    /**
     * Get all items with stock <= min_stock.
     * 
     * @return Collection
     */
    public function getLowStockItems(): Collection;

    /**
     * Adjust stock level of an item.
     * 
     * @param int $id
     * @param int $quantity Change amount (can be positive or negative)
     * @return Inventory
     */
    public function adjustStock(int $id, int $quantity): Inventory;
}

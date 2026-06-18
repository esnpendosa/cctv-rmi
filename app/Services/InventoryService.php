<?php

namespace App\Services;

use App\Models\Inventory;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Events\AuditLogCreated;
use Illuminate\Support\Facades\Auth;

/**
 * Class InventoryService
 * 
 * Handles business logic for inventory management.
 * 
 * @package App\Services
 */
class InventoryService
{
    /**
     * @var InventoryRepositoryInterface
     */
    protected InventoryRepositoryInterface $inventoryRepository;

    /**
     * InventoryService constructor.
     * 
     * @param InventoryRepositoryInterface $inventoryRepository
     */
    public function __construct(InventoryRepositoryInterface $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * Adjust stock level for an inventory item and log the movement.
     * 
     * @param int $inventoryId
     * @param int $quantity Change amount (positive for stock_in, negative for stock_out)
     * @param string $reason The reason for adjustment
     * @param string|null $notes Additional notes
     * @param int|null $userId User performing the action
     * @return Inventory
     */
    public function adjustStock(int $inventoryId, int $quantity, string $reason, ?string $notes = null, ?int $userId = null): Inventory
    {
        $inventory = $this->inventoryRepository->find($inventoryId);
        if (!$inventory) {
            throw new \InvalidArgumentException("Inventory item with ID {$inventoryId} not found.");
        }

        $oldStock = $inventory->stock;
        $action = $quantity >= 0 ? 'stock_in' : 'stock_out';
        
        // Adjust the stock in repository
        $updatedInventory = $this->inventoryRepository->adjustStock($inventoryId, $quantity);
        $newStock = $updatedInventory->stock;

        // Log the stock movement in Audit Log
        event(new AuditLogCreated(
            $userId ?? Auth::id(),
            $action,
            Inventory::class,
            $updatedInventory->id,
            ['stock' => $oldStock],
            [
                'stock' => $newStock,
                'quantity_changed' => $quantity,
                'reason' => $reason,
                'notes' => $notes
            ],
            request()->ip(),
            request()->userAgent()
        ));

        return $updatedInventory;
    }
}

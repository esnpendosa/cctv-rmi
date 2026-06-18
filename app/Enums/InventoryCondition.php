<?php

namespace App\Enums;

/**
 * Class InventoryCondition
 * 
 * Represents the physical condition of an inventory item.
 * 
 * @package App\Enums
 */
enum InventoryCondition: string
{
    case New = 'new';
    case Used = 'used';
    case Damaged = 'damaged';
}

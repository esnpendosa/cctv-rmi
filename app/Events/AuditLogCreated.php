<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class AuditLogCreated
 * 
 * Fired to log an auditable action.
 * 
 * @package App\Events
 */
class AuditLogCreated
{
    use Dispatchable, SerializesModels;

    /**
     * @var int|null
     */
    public ?int $userId;

    /**
     * @var string
     */
    public string $action;

    /**
     * @var string|null
     */
    public ?string $modelType;

    /**
     * @var int|null
     */
    public ?int $modelId;

    /**
     * @var array|null
     */
    public ?array $oldValues;

    /**
     * @var array|null
     */
    public ?array $newValues;

    /**
     * @var string|null
     */
    public ?string $ipAddress;

    /**
     * @var string|null
     */
    public ?string $userAgent;

    /**
     * Create a new event instance.
     * 
     * @param int|null $userId
     * @param string $action
     * @param string|null $modelType
     * @param int|null $modelId
     * @param array|null $oldValues
     * @param array|null $newValues
     * @param string|null $ipAddress
     * @param string|null $userAgent
     */
    public function __construct(
        ?int $userId,
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ) {
        $this->userId = $userId;
        $this->action = $action;
        $this->modelType = $modelType;
        $this->modelId = $modelId;
        $this->oldValues = $oldValues;
        $this->newValues = $newValues;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
    }
}

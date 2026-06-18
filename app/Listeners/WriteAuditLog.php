<?php

namespace App\Listeners;

use App\Events\AuditLogCreated;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class WriteAuditLog
 * 
 * Event listener that writes log entries to the audit_logs table.
 * 
 * @package App\Listeners
 */
class WriteAuditLog implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     * 
     * @param AuditLogCreated $event
     * @return void
     */
    public function handle(AuditLogCreated $event): void
    {
        AuditLog::create([
            'user_id' => $event->userId,
            'action' => $event->action,
            'model_type' => $event->modelType,
            'model_id' => $event->modelId,
            'old_values' => $event->oldValues,
            'new_values' => $event->newValues,
            'ip_address' => $event->ipAddress,
            'user_agent' => $event->userAgent,
        ]);
    }
}

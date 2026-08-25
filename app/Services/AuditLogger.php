<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Log a security or transaction event to the audit trail.
     *
     * @param string $action
     * @param string $description
     * @param string $severity (info, warning, danger)
     * @param \Illuminate\Database\Eloquent\Model|null $auditable
     * @param array|null $oldValues
     * @param array|null $newValues
     * @param int|null $userId
     * @return AuditLog
     */
    public static function log(
        string $action,
        string $description,
        string $severity = 'info',
        $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null
    ) {
        // Resolve actor ID. If explicit userId provided, use it; otherwise use Auth::id().
        $actorId = $userId ?? Auth::id();

        return AuditLog::create([
            'user_id' => $actorId,
            'action' => $action,
            'severity' => $severity,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable ? $auditable->getKey() : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description,
        ]);
    }
}

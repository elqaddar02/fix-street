<?php

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Get the CSS classes for a report status badge
 * 
 * @param string $status Report status (OPEN, IN_PROGRESS, RESOLVED, CLOSED)
 * @return string Tailwind CSS classes
 */
function reportStatusClass($status)
{
    $statusClasses = [
        'OPEN' => 'bg-yellow-100 text-yellow-800',
        'IN_PROGRESS' => 'bg-orange-100 text-orange-800',
        'RESOLVED' => 'bg-green-100 text-green-800',
        'CLOSED' => 'bg-gray-100 text-gray-800',
    ];

    return $statusClasses[$status] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Log admin actions for audit trail
 * 
 * @param string $action The action performed (Create, Update, Delete)
 * @param string $model The model/entity name
 * @param int|null $modelId The ID of the affected model
 * @param string|null $description Additional description
 */
function logAdminAction($action, $model, $modelId = null, $description = null)
{
    if (!Auth::check()) {
        return;
    }

    AuditLog::create([
        'admin_id' => Auth::id(),
        'action' => $action,
        'model' => $model,
        'model_id' => $modelId,
        'description' => $description,
        'ip_address' => Request::ip(),
    ]);
}

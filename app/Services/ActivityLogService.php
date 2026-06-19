<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    public static function log(
        string $action,
        string $description,
        ?string $modelType = null,
        ?int $modelId = null,
        array $oldValues = [],
        array $newValues = []
    ): void {
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'description' => $description,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
            'old_values'  => $oldValues ?: null,
            'new_values'  => $newValues ?: null,
        ]);
    }
}

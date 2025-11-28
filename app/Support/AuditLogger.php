<?php

namespace App\Support;

use App\Models\Admin\Master\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function record(
        string $action,
        ?Model $entity = null,
        array $properties = [],
        ?string $description = null
    ): void {
        $request = request();
        $user = Auth::user();
        $userName = $user?->name ?? $user?->email ?? 'System';

        AuditLog::create([
            'user_id' => $user?->getAuthIdentifier(),
            'user_name' => $userName,
            'action' => $action,
            'entity_type' => $entity ? get_class($entity) : ($properties['entity_type'] ?? null),
            'entity_id' => $entity?->getKey(),
            'description' => $description,
            'properties' => $properties,
            'ip_address' => $request?->ip(),
            'acted_at' => now(),
        ]);
    }
}


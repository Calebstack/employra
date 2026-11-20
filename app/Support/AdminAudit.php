<?php

namespace App\Support;

use App\Models\AdminActivityLog;
use App\Models\Admin;

class AdminAudit
{
    public static function record(Admin $admin, string $action, ?string $targetType, ?int $targetId, string $details): void
    {
        AdminActivityLog::create([
            'admin_id' => $admin->id,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'details' => $details,
        ]);
    }
}

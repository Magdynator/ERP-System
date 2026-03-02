<?php

declare(strict_types=1);

namespace Erp\Core\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HasAuditLog
{
    public static function bootHasAuditLog(): void
    {
        static::created(function ($model) {
            static::logAudit('created', $model);
        });

        static::updated(function ($model) {
            static::logAudit('updated', $model);
        });

        static::deleted(function ($model) {
            static::logAudit('deleted', $model);
        });
    }

    protected static function logAudit(string $action, $model): void
    {
        $oldValues = null;
        $newValues = null;

        if ($action === 'created') {
            $newValues = $model->getAttributes();
            $newValues['_ip_address'] = request()->ip();
        } elseif ($action === 'updated') {
            $newValues = $model->getChanges();
            $oldValues = array_intersect_key($model->getOriginal(), $newValues);
            $newValues['_ip_address'] = request()->ip();
        } elseif ($action === 'deleted') {
            $oldValues = $model->getAttributes();
            $oldValues['_ip_address'] = request()->ip();
        }

        \Erp\Core\Models\AuditLog::create([
            'action' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'user_id' => Auth::id(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}

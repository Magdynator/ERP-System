<?php

declare(strict_types=1);

namespace Erp\Core\Services;

use Erp\Core\Models\AuditLog;
use Erp\Core\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogService
{
    public function getPaginatedLogs(
        ?int $userId = null,
        ?string $action = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $search = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($action) {
            $query->where('action', $action);
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('auditable_type', 'LIKE', "%{$search}%")
                  ->orWhere('action', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getDistinctActions(): Collection
    {
        return AuditLog::select('action')->distinct()->pluck('action');
    }

    public function exportCsv(
        ?int $userId = null,
        ?string $action = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $search = null
    ): StreamedResponse {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($action) {
            $query->where('action', $action);
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('auditable_type', 'LIKE', "%{$search}%")
                  ->orWhere('action', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $fileName = 'audit_logs_' . date('Y_m_d_His') . '.csv';

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = ['ID', 'User', 'Action', 'Model Type', 'Model ID', 'Date', 'IP Address', 'Old Values', 'New Values'];

        $callback = function () use ($query, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $query->chunk(1000, function ($logs) use ($file) {
                foreach ($logs as $log) {
                    $oldValuesIP = $log->old_values['_ip_address'] ?? null;
                    $newValuesIP = $log->new_values['_ip_address'] ?? null;
                    $ipAddress = $log->ip_address ?? $newValuesIP ?? $oldValuesIP ?? 'N/A';

                    $oldValues = $log->old_values;
                    $newValues = $log->new_values;

                    if (isset($oldValues['_ip_address'])) {
                        unset($oldValues['_ip_address']);
                    }
                    if (isset($newValues['_ip_address'])) {
                        unset($newValues['_ip_address']);
                    }

                    fputcsv($file, [
                        $log->id,
                        $log->user ? $log->user->name : 'System',
                        $log->action,
                        class_basename($log->auditable_type),
                        $log->auditable_id,
                        $log->created_at->format('Y-m-d H:i:s'),
                        $ipAddress,
                        $oldValues ? json_encode($oldValues) : '',
                        $newValues ? json_encode($newValues) : '',
                    ]);
                }
            });
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function logAction(string $action, string $auditableType, int $auditableId, ?int $userId = null, ?array $newValues = null, ?array $oldValues = null): AuditLog
    {
        return AuditLog::create([
            'action'         => $action,
            'auditable_type' => $auditableType,
            'auditable_id'   => $auditableId,
            'user_id'        => $userId,
            'new_values'     => $newValues,
            'old_values'     => $oldValues,
        ]);
    }
}

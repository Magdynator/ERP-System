<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Erp\Core\Models\AuditLog;
use Erp\Core\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the audit logs.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Only admins can view audit logs.');
        }

        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Since this might be large, we search across model or action
                $q->where('auditable_type', 'LIKE', "%{$search}%")
                  ->orWhere('action', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->has('export') && $request->export === 'csv') {
            return $this->exportCsv($query);
        }

        $logs = $query->paginate(15)->withQueryString();
        
        $users = User::orderBy('name')->get();
        
        $actions = AuditLog::select('action')->distinct()->pluck('action');

        return view('audit-logs.index', compact('logs', 'users', 'actions'));
    }
    
    /**
     * Show a single audit log.
     */
    public function show(AuditLog $auditLog): View
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Only admins can view audit logs.');
        }

        return view('audit-logs.show', compact('auditLog'));
    }

    /**
     * Helper to export to CSV.
     */
    protected function exportCsv($query)
    {
        $fileName = 'audit_logs_' . date('Y_m_d_His') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ["ID", "User", "Action", "Model Type", "Model ID", "Date", "IP Address", "Old Values", "New Values"];

        $callback = function() use($query, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $query->chunk(1000, function($logs) use ($file) {
                foreach ($logs as $log) {
                    $oldValuesIP = $log->old_values['_ip_address'] ?? null;
                    $newValuesIP = $log->new_values['_ip_address'] ?? null;
                    $ipAddress = $log->ip_address ?? $newValuesIP ?? $oldValuesIP ?? 'N/A';

                    // Cleanup old and new for export
                    $oldValues = $log->old_values;
                    $newValues = $log->new_values;
                    
                    if(isset($oldValues['_ip_address'])) unset($oldValues['_ip_address']);
                    if(isset($newValues['_ip_address'])) unset($newValues['_ip_address']);
                    
                    fputcsv($file, [
                        $log->id,
                        $log->user ? $log->user->name : 'System',
                        $log->action,
                        class_basename($log->auditable_type),
                        $log->auditable_id,
                        $log->created_at->format('Y-m-d H:i:s'),
                        $ipAddress,
                        $oldValues ? json_encode($oldValues) : '',
                        $newValues ? json_encode($newValues) : ''
                    ]);
                }
            });
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

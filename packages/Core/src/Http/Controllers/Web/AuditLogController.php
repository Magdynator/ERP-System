<?php

declare(strict_types=1);

namespace Erp\Core\Http\Controllers\Web;

use Erp\Core\Http\Controllers\Controller;
use Erp\Core\Models\User;
use Erp\Core\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function index(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Only admins can view audit logs.');
        }

        if ($request->has('export') && $request->export === 'csv') {
            return $this->auditLogService->exportCsv(
                $request->user_id ? (int) $request->user_id : null,
                $request->action ?: null,
                $request->date_from ?: null,
                $request->date_to ?: null,
                $request->search ?: null
            );
        }

        $logs = $this->auditLogService->getPaginatedLogs(
            $request->user_id ? (int) $request->user_id : null,
            $request->action ?: null,
            $request->date_from ?: null,
            $request->date_to ?: null,
            $request->search ?: null,
            15
        );

        $users   = User::orderBy('name')->get();
        $actions = $this->auditLogService->getDistinctActions();

        return view('audit-logs.index', compact('logs', 'users', 'actions'));
    }

    public function show(\Erp\Core\Models\AuditLog $auditLog): View
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized. Only admins can view audit logs.');
        }

        return view('audit-logs.show', compact('auditLog'));
    }
}

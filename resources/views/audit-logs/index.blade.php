@extends('layouts.app')
@section('title', 'Security Logs')

@section('content')
<div class="page-header reveal">
    <div class="page-title-section">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-12 h-12 rounded-2xl bg-gray-900 text-white flex items-center justify-center shadow-2xl shadow-gray-900/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.333 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.956 11.956 0 0112 2.714z"/></svg>
            </div>
            <h1 class="heading-1">Audit Logs</h1>
        </div>
        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest px-1">Real-time system behavior & event tracking</p>
    </div>
    
    <div class="flex items-center gap-3">
        <a href="{{ route('web.audit-logs.index', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            Export Ledger
        </a>
    </div>
</div>

{{-- Filters Matrix --}}
<div class="card mb-8 p-6 reveal">
    <form method="GET" action="{{ route('web.audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <div>
            <label class="form-label uppercase tracking-widest text-[10px]">Model Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ID or Action..." class="input">
        </div>
        <div>
            <label class="form-label uppercase tracking-widest text-[10px]">Actor</label>
            <select name="user_id" class="select">
                <option value="">All Personnel</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label uppercase tracking-widest text-[10px]">Event Type</label>
            <select name="action" class="select">
                <option value="">All Events</option>
                @foreach($actions as $actionOpt)
                    @if($actionOpt)
                        <option value="{{ $actionOpt }}" {{ request('action') == $actionOpt ? 'selected' : '' }}>
                            {{ ucfirst($actionOpt) }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label uppercase tracking-widest text-[10px]">Interval Start</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="input">
        </div>
        <div class="flex items-end gap-2">
            <div class="flex-1">
                <label class="form-label uppercase tracking-widest text-[10px]">Interval End</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="input">
            </div>
            <button type="submit" class="btn-primary h-11 px-5">Apply</button>
            <a href="{{ route('web.audit-logs.index') }}" class="btn-ghost h-11 px-4 flex items-center justify-center">Reset</a>
        </div>
    </form>
</div>

<div class="table-card reveal">
    @if($logs->count() > 0)
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Personnel</th>
                    <th>Event</th>
                    <th>Node Impacted</th>
                    <th>Network Source</th>
                    <th class="text-right">Intelligence</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    @php
                       $shortModel = class_basename($log->auditable_type);
                       if ($log->auditable_type === \App\Models\User::class) $shortModel = 'User';
                       
                       $oldIP = $log->old_values['_ip_address'] ?? null;
                       $newIP = $log->new_values['_ip_address'] ?? null;
                       $ip = $log->ip_address ?? $newIP ?? $oldIP ?? 'N/A';
                       
                       $actionClass = 'bg-gray-100 text-gray-600';
                       if($log->action === 'created') $actionClass = 'bg-emerald-50 text-emerald-600';
                       if($log->action === 'updated') $actionClass = 'bg-blue-50 text-blue-600';
                       if($log->action === 'deleted') $actionClass = 'bg-rose-50 text-rose-600';
                       if($log->action === 'login') $actionClass = 'bg-brand-50 text-brand-600';
                    @endphp
                    <tr class="group hover:bg-white/40 transition-all">
                        <td class="px-6 py-5 font-bold text-gray-900 text-sm">
                            {{ $log->created_at->format('M d, H:i:s') }}
                        </td>
                        <td class="px-6 py-5 text-gray-500 font-medium">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg bg-gray-100 flex items-center justify-center text-[10px] font-black text-gray-500">
                                    {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                </div>
                                {{ $log->user ? $log->user->name : 'System Core' }}
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="status-badge {{ $actionClass }} uppercase tracking-widest text-[9px] font-black">
                                {{ strtoupper($log->action) }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-gray-500 font-medium text-sm">
                            {{ $shortModel }} <span class="text-xs text-gray-400 font-black">#{{ $log->auditable_id }}</span>
                        </td>
                        <td class="px-6 py-5 font-mono text-gray-400 text-xs">
                            {{ $ip }}
                        </td>
                        <td class="px-6 py-5 text-right">
                            <a href="{{ route('web.audit-logs.show', $log) }}" class="text-brand-500 hover:text-brand-700 text-xs font-black uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">Analyze →</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        @if($logs->hasPages())
        <div class="p-6 border-t border-gray-100/30">
            {{ $logs->links() }}
        </div>
        @endif
    @else
        <div class="empty-state py-24">
            <div class="w-16 h-16 rounded-4xl bg-gray-50 flex items-center justify-center mx-auto mb-6 text-gray-300">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-black text-gray-900 tracking-tighter">Zero Event Matches</h3>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">No security events found for the current matrix.</p>
        </div>
    @endif
</div>
@endsection

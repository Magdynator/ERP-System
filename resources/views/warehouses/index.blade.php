@extends('layouts.app')
@section('title', 'Logistics Nodes')

@section('content')
<div class="page-header reveal">
    <div class="page-title-section">
        <h1 class="heading-1">Warehouses</h1>
        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">Physical distribution & storage infrastructure</p>
    </div>
    @can('add-warehouse')
    <a href="{{ route('web.warehouses.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Establish Node
    </a>
    @endcan
</div>

<div class="table-card reveal mt-8">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Node Name</th>
                <th>System Code</th>
                <th>Status</th>
                <th class="text-right w-32">Operations</th>
            </tr>
        </thead>
        <tbody>
            @forelse($warehouses as $w)
            <tr class="group hover:bg-white/40 transition-all">
                <td class="px-6 py-5 font-black text-gray-900 tracking-tighter">{{ $w->name }}</td>
                <td class="px-6 py-5 font-mono text-gray-400 text-xs tracking-widest uppercase">{{ $w->code }}</td>
                <td class="px-6 py-5">
                    @if($w->is_active)
                        <span class="status-badge status-active px-3 py-1 text-[9px] font-black uppercase tracking-widest">Online</span>
                    @else
                        <span class="status-badge status-inactive px-3 py-1 text-[9px] font-black uppercase tracking-widest">Offline</span>
                    @endif
                </td>
                <td class="px-6 py-5 text-right">
                    @can('add-warehouse')
                    <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('web.warehouses.edit', $w) }}" class="text-xs font-black text-brand-500 hover:text-brand-700 uppercase tracking-widest transition-colors">Configure</a>
                        <form action="{{ route('web.warehouses.destroy', $w) }}" method="POST" class="inline" onsubmit="return confirm('Decommission this node?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-black text-rose-500 hover:text-rose-700 uppercase tracking-widest transition-colors">Terminate</button>
                        </form>
                    </div>
                    @endcan
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="empty-state py-20 text-center">
                    <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-6 text-gray-300">
                         <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 tracking-tighter">Infrastructure Empty</h3>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">Establish your first logistics node to begin.</p>
                    @can('add-warehouse')
                    <a href="{{ route('web.warehouses.create') }}" class="btn-primary mt-8">Configure Node</a>
                    @endcan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($warehouses->hasPages())
    <div class="p-6 border-t border-gray-100/30">
        {{ $warehouses->links() }}
    </div>
    @endif
</div>
@endsection

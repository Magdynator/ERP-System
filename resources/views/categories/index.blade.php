@extends('layouts.app')
@section('title', 'Division Registry')

@section('content')
<div class="page-header reveal">
    <div class="page-title-section">
        <h1 class="heading-1">Categories</h1>
        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">Logical classification & taxonomy control</p>
    </div>
    @can('manage-categories')
    <a href="{{ route('web.categories.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Establish Division
    </a>
    @endcan
</div>

<div class="table-card reveal mt-8">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Division Name</th>
                <th>Slug (Identity)</th>
                <th>Node Count</th>
                <th>Status</th>
                <th class="text-right w-32">Operations</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $c)
            <tr class="group hover:bg-white/40 transition-all">
                <td class="px-6 py-5 font-black text-gray-900 tracking-tighter">{{ $c->name }}</td>
                <td class="px-6 py-5 font-mono text-gray-400 text-xs tracking-widest uppercase">{{ $c->slug }}</td>
                <td class="px-6 py-5">
                    <span class="inline-flex items-center justify-center min-w-[2rem] h-8 px-3 rounded-xl bg-gray-900 text-[10px] font-black text-white shadow-lg shadow-gray-900/20">
                        {{ $c->products_count }}
                    </span>
                </td>
                <td class="px-6 py-5">
                    @if($c->is_active)
                        <span class="status-badge status-active px-3 py-1 text-[9px] font-black uppercase tracking-widest">Active</span>
                    @else
                        <span class="status-badge status-inactive px-3 py-1 text-[9px] font-black uppercase tracking-widest">Inactive</span>
                    @endif
                </td>
                <td class="px-6 py-5 text-right">
                    @can('manage-categories')
                    <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('web.categories.edit', $c) }}" class="text-xs font-black text-brand-500 hover:text-brand-700 uppercase tracking-widest transition-colors">Modify</a>
                        <form action="{{ route('web.categories.destroy', $c) }}" method="POST" class="inline" onsubmit="return confirm('Delete this division node?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-black text-rose-500 hover:text-rose-700 uppercase tracking-widest transition-colors">Archive</button>
                        </form>
                    </div>
                    @endcan
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="empty-state py-20 text-center">
                    <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-6 text-gray-300">
                         <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 tracking-tighter">Division List Empty</h3>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">Create your first division node to begin.</p>
                    @can('manage-categories')
                    <a href="{{ route('web.categories.create') }}" class="btn-primary mt-8">Establish Division</a>
                    @endcan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($categories->hasPages())
    <div class="p-6 border-t border-gray-100/30">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection

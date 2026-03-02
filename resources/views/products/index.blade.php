@extends('layouts.app')
@section('title', 'Inventory Core')

@section('content')
<div class="page-header reveal">
    <div class="page-title-section">
        <h1 class="heading-1">Products</h1>
        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">Global catalog and pricing registry</p>
    </div>
    @can('manage-products')
    <a href="{{ route('web.products.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Provision Product
    </a>
    @endcan
</div>

{{-- Filters Registry --}}
<div class="card mb-8 p-6 reveal">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-6 items-end">
        <div class="form-group">
            <label for="category" class="form-label uppercase tracking-widest text-[10px]">Division/Category</label>
            <select name="category_id" id="category" class="select">
                <option value="">All Divisions</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="pb-3">
            <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" name="active_only" value="1" {{ request('active_only') ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-gray-200 text-brand-500 focus:ring-brand-500 transition-all cursor-pointer">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-gray-600">Active Nodes Only</span>
            </label>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-secondary flex-1 py-3 text-[10px] uppercase tracking-widest font-black">Apply Matrix</button>
            <a href="{{ route('web.products.index') }}" class="btn-ghost px-4 py-3 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>
    </form>
</div>

<div class="table-card reveal">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Product Identifier</th>
                <th>SKU Code</th>
                <th>Division</th>
                <th class="text-right">Valuation (Cost/Sell)</th>
                <th>Status</th>
                <th class="text-right w-32">Operations</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $p)
            <tr class="group hover:bg-white/40 transition-all cursor-pointer" onclick="window.location='{{ route('web.products.show', $p) }}'">
                <td class="px-6 py-5">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl overflow-hidden bg-white/50 border border-white shrink-0">
                            <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                        </div>
                        <a href="{{ route('web.products.show', $p) }}" class="font-black text-gray-900 tracking-tighter hover:text-brand-500 transition-colors">{{ $p->name }}</a>
                    </div>
                </td>
                <td class="px-6 py-5 font-mono text-gray-400 text-xs tracking-widest uppercase">{{ $p->sku }}</td>
                <td class="px-6 py-5">
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest bg-gray-100 px-2 py-1 rounded-lg">
                        {{ $p->category?->name ?? 'Unclassified' }}
                    </span>
                </td>
                <td class="px-6 py-5 text-right font-black text-gray-900 tracking-tight">
                    <span class="text-gray-400 text-[10px] mr-1 uppercase">Price</span> {{ number_format($p->selling_price, 2) }}
                </td>
                <td class="px-6 py-5">
                    @if($p->is_active)
                        <span class="status-badge status-active px-3 py-1 text-[9px] font-black uppercase tracking-widest">Active Node</span>
                    @else
                        <span class="status-badge status-inactive px-3 py-1 text-[9px] font-black uppercase tracking-widest">Offline</span>
                    @endif
                </td>
                <td class="px-6 py-5 text-right">
                    @can('manage-products')
                    <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('web.products.edit', $p) }}" class="text-xs font-black text-brand-500 hover:text-brand-700 uppercase tracking-widest transition-colors">Modify</a>
                        <form action="{{ route('web.products.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Archive this node?');">
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
                <td colspan="6" class="empty-state py-20 text-center">
                    <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-6 text-gray-300">
                         <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 tracking-tighter">Inventory Empty</h3>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">Provision your first product to begin.</p>
                    @can('manage-products')
                    <a href="{{ route('web.products.create') }}" class="btn-primary mt-8">Register Product</a>
                    @endcan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($products->hasPages())
    <div class="p-6 border-t border-gray-100/30">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection

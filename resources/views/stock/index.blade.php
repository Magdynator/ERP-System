@extends('layouts.app')
@section('title', 'Stock Matrix')

@section('content')
<div class="page-header reveal">
    <div class="page-title-section">
        <h1 class="heading-1">Stock Report</h1>
        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">Real-time inventory distribution & node levels</p>
    </div>
</div>

<div class="card mb-8 p-6 reveal">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-end">
        <div class="form-group flex-1">
            <label for="product_id" class="form-label uppercase tracking-widest text-[10px]">Product Node</label>
            <select name="product_id" id="product_id" required class="select">
                <option value="">Select Target Node</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->sku }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-group flex-1">
            <label for="warehouse_id" class="form-label uppercase tracking-widest text-[10px]">Logistics Node (Optional)</label>
            <select name="warehouse_id" id="warehouse_id" class="select">
                <option value="">All Global Nodes</option>
                @foreach($warehouses as $w)
                    <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary flex-1 py-3 text-[10px] uppercase tracking-widest font-black">Scan Matrix</button>
            <a href="{{ route('web.stock.index') }}" class="btn-ghost px-4 py-3 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>
    </form>
</div>

@if(!empty($stock))
<div class="table-card reveal">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Product Node</th>
                <th>Logistics Node</th>
                <th class="text-right">Quantum (Qty)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stock as $s)
            <tr class="group hover:bg-white/40 transition-all">
                <td class="px-6 py-5">
                    <div class="font-black text-gray-900 tracking-tighter">{{ $s['product']->name ?? 'Unidentified Node' }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $s['product']->sku ?? 'N/A' }}</div>
                </td>
                <td class="px-6 py-5 font-bold text-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                        {{ $s['warehouse']->name ?? 'Transit' }}
                    </div>
                </td>
                <td class="px-6 py-5 text-right font-black text-gray-900 tracking-tight text-lg">
                    {{ number_format($s['quantity'], 2) }}
                    <span class="text-[10px] text-gray-400 uppercase ml-1">Units</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
    @if(request()->has('product_id'))
        <div class="empty-state py-24 reveal text-center">
            <div class="w-16 h-16 rounded-4xl bg-gray-50 flex items-center justify-center mx-auto mb-6 text-gray-300">
                 <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
            </div>
            <h3 class="text-xl font-black text-gray-900 tracking-tighter">Negative Matrix Result</h3>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">Zero stock detected for the selected nodes.</p>
        </div>
    @endif
@endif
@endsection

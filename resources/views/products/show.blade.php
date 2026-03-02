@extends('layouts.app')
@section('title', $product->name . ' - Product Profile')
@section('nav-context', 'Products')

@section('content')
<a href="{{ route('web.products.index') }}" class="link-back reveal">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    Products
</a>

<div class="mt-8 max-w-5xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        
        <!-- Left Column: Image -->
        <div class="md:col-span-4 reveal">
            <div class="glass-card p-2 rounded-4xl bg-white/40 shadow-sm border border-white/60">
                <div class="aspect-square rounded-[30px] overflow-hidden bg-white/60 relative group">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                    @if(!$product->is_active)
                    <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-[2px] flex items-center justify-center">
                        <span class="status-badge bg-gray-900/80 text-white border-0 font-bold uppercase tracking-widest text-xs px-4 py-2">Archived</span>
                    </div>
                    @endif
                </div>
            </div>
            
            @can('manage-products')
            <div class="mt-6 flex justify-center">
                <a href="{{ route('web.products.edit', $product) }}" class="btn-primary w-full justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.89 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.89l10.66-10.66zM16.862 4.487L19.5 7.125" /></svg>
                    Edit Product
                </a>
            </div>
            @endcan
        </div>

        <!-- Right Column: Details -->
        <div class="md:col-span-8 space-y-8">
            <div class="reveal" style="transition-delay: 100ms;">
                <div class="flex items-center gap-3 mb-2">
                    <span class="status-badge {{ $product->is_active ? 'status-success' : 'status-neutral' }}">
                        {{ $product->is_active ? 'ACTIVE' : 'INACTIVE' }}
                    </span>
                    <span class="text-xs font-bold tracking-widest text-brand-500 uppercase bg-brand-500/10 px-3 py-1 rounded-full">
                        {{ $product->category?->name ?? 'UNCATEGORIZED' }}
                    </span>
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tighter mb-2">{{ $product->name }}</h1>
                <p class="font-mono text-gray-500 text-sm">SKU: {{ $product->sku }}</p>
            </div>

            <!-- Pricing Matrix -->
            <div class="glass-card p-8 reveal" style="transition-delay: 200ms;">
                <h3 class="heading-3 mb-6">Financial Metrics</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="p-6 rounded-3xl bg-white/40 border border-white/50">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Cost Price</p>
                        <p class="text-2xl font-black text-gray-900">${{ number_format($product->cost_price, 2) }}</p>
                    </div>
                    <div class="p-6 rounded-3xl bg-brand-500/5 border border-brand-500/20 relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-16 h-16 bg-brand-500/10 rounded-full blur-xl"></div>
                        <p class="text-xs font-bold text-brand-600 uppercase tracking-widest mb-1">Selling Price</p>
                        <p class="text-3xl font-black text-brand-700">${{ number_format($product->selling_price, 2) }}</p>
                    </div>
                    <div class="p-6 rounded-3xl bg-white/40 border border-white/50">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Tax Rate</p>
                        <p class="text-2xl font-black text-gray-900">{{ number_format($product->tax_percentage, 1) }}%</p>
                    </div>
                </div>
            </div>
            
            <!-- Metadata -->
            <div class="glass-card p-8 reveal" style="transition-delay: 300ms;">
                 <h3 class="heading-3 mb-6">System Metadata</h3>
                 <div class="grid grid-cols-2 gap-y-4 text-sm">
                     <div class="text-gray-500 font-medium">Record ID</div>
                     <div class="font-mono text-gray-900 font-bold justify-self-end">#{{ $product->id }}</div>
                     
                     <div class="text-gray-500 font-medium pt-4 border-t border-gray-100/50">Created At</div>
                     <div class="text-gray-900 font-medium pt-4 border-t border-gray-100/50 justify-self-end">{{ $product->created_at->format('M d, Y H:i') }}</div>
                     
                     <div class="text-gray-500 font-medium pt-4 border-t border-gray-100/50">Last Update</div>
                     <div class="text-gray-900 font-medium pt-4 border-t border-gray-100/50 justify-self-end">{{ $product->updated_at->format('M d, Y H:i') }}</div>
                 </div>
            </div>

        </div>
    </div>
</div>

@endsection

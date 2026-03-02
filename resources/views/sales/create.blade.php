@extends('layouts.app')
@section('title', 'New Sale')
@section('nav-context', 'Sales')
@section('content')

<a href="{{ route('web.sales.index') }}" class="link-back">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    Sales
</a>

<div class="max-w-2xl">
    <h1 class="heading-1 mb-8">Create Sale</h1>
    <div class="form-card" id="sale-form-wrapper">
        <form action="{{ route('web.sales.store') }}" method="POST" class="flex flex-col gap-6" id="sale-form">
            @csrf
            <div class="form-group">
                <label for="warehouse_id" class="form-label">Warehouse *</label>
                <select name="warehouse_id" id="warehouse_id" required class="select @error('warehouse_id') input-error @enderror">
                    <option value="">Select warehouse</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                    @endforeach
                </select>
                @error('warehouse_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="form-group">
                    <label for="customer_name" class="form-label">Customer name</label>
                    <input id="customer_name" type="text" name="customer_name" value="{{ old('customer_name') }}" class="input">
                </div>
                <div class="form-group">
                    <label for="customer_email" class="form-label">Customer email</label>
                    <input id="customer_email" type="email" name="customer_email" value="{{ old('customer_email') }}" class="input">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Items *</label>
                <div id="sale-items" class="flex flex-col gap-3">
                    @foreach(old('items', [['product_id' => '', 'quantity' => '']]) as $idx => $item)
                    <div class="flex gap-3 items-center flex-wrap p-3 rounded-xl bg-gray-50/80">
                        <select name="items[{{ $idx }}][product_id]" class="select flex-1 min-w-[200px]" required>
                            <option value="">Product</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ ($item['product_id'] ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }} — {{ number_format($p->selling_price, 2) }}</option>
                            @endforeach
                        </select>
                        <input type="number" step="0.01" name="items[{{ $idx }}][quantity]" value="{{ $item['quantity'] ?? '' }}" placeholder="Qty" required min="0.01" class="input w-28">
                    </div>
                    @endforeach
                </div>
                <button type="button" onclick="addSaleItem()" class="link mt-2 text-sm inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Add item
                </button>
            </div>
            <div class="form-group">
                <label for="currency" class="form-label">Currency</label>
                <input id="currency" type="text" name="currency" value="{{ old('currency', 'USD') }}" maxlength="3" class="input w-28">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Create Sale</button>
                <a href="{{ route('web.sales.index') }}" class="btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
<template id="sale-item-tpl">
    <div class="flex gap-3 items-center flex-wrap p-3 rounded-xl bg-gray-50/80">
        <select name="items[__INDEX__][product_id]" class="select flex-1 min-w-[200px]" required><option value="">Product</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} — {{ number_format($p->selling_price, 2) }}</option>@endforeach</select>
        <input type="number" step="0.01" name="items[__INDEX__][quantity]" placeholder="Qty" required min="0.01" class="input w-28">
    </div>
</template>
<script>
let saleItemIndex = {{ count(old('items', [['product_id' => '', 'quantity' => '']])) }};
function addSaleItem() {
    const tpl = document.getElementById('sale-item-tpl').innerHTML.replace(/__INDEX__/g, saleItemIndex);
    document.getElementById('sale-items').insertAdjacentHTML('beforeend', tpl);
    saleItemIndex++;
}
</script>
@endsection

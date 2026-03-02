@extends('layouts.app')
@section('title', 'Add Product')
@section('nav-context', 'Products')
@section('content')

<a href="{{ route('web.products.index') }}" class="link-back">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    Back to Products
</a>

<div class="max-w-2xl">
    <h1 class="heading-1 mb-8">Create Product</h1>
    <div class="form-card">
        <form action="{{ route('web.products.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
            @csrf
            <div class="form-group">
                <label for="name" class="form-label">Name *</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required class="input @error('name') input-error @enderror">
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            
            <div class="form-group mb-2">
                <label for="image" class="form-label">Product Image</label>
                <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" class="input p-2 bg-white/50 @error('image') input-error @enderror">
                <p class="text-xs text-gray-400 mt-1">Recommended size: 800x800px. Max 2MB.</p>
                @error('image')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="form-group">
                    <label for="sku" class="form-label">SKU *</label>
                    <input id="sku" type="text" name="sku" value="{{ old('sku') }}" required class="input @error('sku') input-error @enderror">
                    @error('sku')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="category_id" class="form-label">Category</label>
                    <select name="category_id" id="category_id" class="select">
                        <option value="">— None —</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="form-group">
                    <label for="cost_price" class="form-label">Cost price *</label>
                    <input id="cost_price" type="number" step="0.01" name="cost_price" value="{{ old('cost_price') }}" required class="input @error('cost_price') input-error @enderror">
                    @error('cost_price')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="selling_price" class="form-label">Selling price *</label>
                    <input id="selling_price" type="number" step="0.01" name="selling_price" value="{{ old('selling_price') }}" required class="input @error('selling_price') input-error @enderror">
                    @error('selling_price')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="tax_percentage" class="form-label">Tax %</label>
                    <input id="tax_percentage" type="number" step="0.01" name="tax_percentage" value="{{ old('tax_percentage', 0) }}" class="input">
                </div>
            </div>

            <div class="form-section">
                <h3 class="heading-3 mb-1">Initial stock</h3>
                <p class="body-sm mb-4">Set up opening inventory at a specific location.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="form-group">
                        <label for="warehouse_id" class="form-label">Warehouse *</label>
                        <select name="warehouse_id" id="warehouse_id" required class="select @error('warehouse_id') input-error @enderror">
                            <option value="">— Select location —</option>
                            @foreach(($warehouses ?? []) as $w)
                                <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                            @endforeach
                        </select>
                        @error('warehouse_id')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="initial_quantity" class="form-label">Quantity *</label>
                        <input id="initial_quantity" type="number" step="0.01" name="initial_quantity" value="{{ old('initial_quantity') }}" min="0.01" placeholder="0" required class="input @error('initial_quantity') input-error @enderror">
                        @error('initial_quantity')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer w-fit">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="checkbox">
                    <span class="text-sm font-medium text-gray-700">Product is active</span>
                </label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">Create Product</button>
                <a href="{{ route('web.products.index') }}" class="btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

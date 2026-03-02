@extends('layouts.app')
@section('title', 'Edit Product')
@section('nav-context', 'Products')
@section('content')

<a href="{{ route('web.products.index') }}" class="link-back">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    Products
</a>

<div class="max-w-2xl">
    <h1 class="heading-1 mb-8">Edit Product</h1>
    <div class="form-card">
        <form action="{{ route('web.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name" class="form-label">Name *</label>
                <input id="name" type="text" name="name" value="{{ old('name', $product->name) }}" required class="input @error('name') input-error @enderror">
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group mb-2">
                <label for="image" class="form-label">Product Image</label>
                @if($product->image_path)
                <div class="mb-3 relative w-24 h-24 rounded-2xl overflow-hidden border border-gray-100/50 shadow-sm">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
                @endif
                <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" class="input p-2 bg-white/50 @error('image') input-error @enderror">
                <p class="text-xs text-gray-400 mt-1">Upload a new image to replace the current one. Max 2MB.</p>
                @error('image')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            
            <div class="form-group">
                <label for="sku" class="form-label">SKU *</label>
                <input id="sku" type="text" name="sku" value="{{ old('sku', $product->sku) }}" required class="input @error('sku') input-error @enderror">
                @error('sku')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="select">
                    <option value="">— None —</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div class="form-group">
                    <label for="cost_price" class="form-label">Cost price *</label>
                    <input id="cost_price" type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" required class="input">
                </div>
                <div class="form-group">
                    <label for="selling_price" class="form-label">Selling price *</label>
                    <input id="selling_price" type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required class="input">
                </div>
            </div>
            <div class="form-group">
                <label for="tax_percentage" class="form-label">Tax %</label>
                <input id="tax_percentage" type="number" step="0.01" name="tax_percentage" value="{{ old('tax_percentage', $product->tax_percentage) }}" class="input">
            </div>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="checkbox">
                <span class="body">Active</span>
            </label>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Update Product</button>
                <a href="{{ route('web.products.index') }}" class="btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

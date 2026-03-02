@extends('layouts.app')
@section('title', 'Add Warehouse')
@section('nav-context', 'Warehouses')
@section('content')

<a href="{{ route('web.warehouses.index') }}" class="link-back">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    Warehouses
</a>

<div class="max-w-2xl">
    <h1 class="heading-1 mb-8">Create Warehouse</h1>
    <div class="form-card">
        <form action="{{ route('web.warehouses.store') }}" method="POST" class="flex flex-col gap-6">
            @csrf
            <div class="grid grid-cols-2 gap-5">
                <div class="form-group">
                    <label for="name" class="form-label">Name *</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required class="input @error('name') input-error @enderror">
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="code" class="form-label">Code *</label>
                    <input id="code" type="text" name="code" value="{{ old('code') }}" required class="input @error('code') input-error @enderror">
                    @error('code')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="checkbox">
                <span class="body">Active</span>
            </label>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Create Warehouse</button>
                <a href="{{ route('web.warehouses.index') }}" class="btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

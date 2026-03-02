@extends('layouts.app')
@section('title', 'Edit Category')
@section('nav-context', 'Categories')
@section('content')

<a href="{{ route('web.categories.index') }}" class="link-back">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    Categories
</a>

<div class="max-w-2xl">
    <h1 class="heading-1 mb-8">Edit Category</h1>
    <div class="form-card">
        <form action="{{ route('web.categories.update', $category) }}" method="POST" class="flex flex-col gap-6">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name" class="form-label">Name *</label>
                <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}" required class="input @error('name') input-error @enderror">
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label for="slug" class="form-label">Slug</label>
                <input id="slug" type="text" name="slug" value="{{ old('slug', $category->slug) }}" class="input @error('slug') input-error @enderror">
                @error('slug')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label for="parent_id" class="form-label">Parent</label>
                <select name="parent_id" id="parent_id" class="select">
                    <option value="">— None —</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}" {{ old('parent_id', $category->parent_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" rows="2" class="textarea">{{ old('description', $category->description) }}</textarea>
            </div>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="checkbox">
                <span class="body">Active</span>
            </label>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Update Category</button>
                <a href="{{ route('web.categories.index') }}" class="btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

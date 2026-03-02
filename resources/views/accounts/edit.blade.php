@extends('layouts.app')
@section('title', 'Edit Account')
@section('nav-context', 'Accounts')
@section('content')

<a href="{{ route('web.accounts.index') }}" class="link-back">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    Accounts
</a>

<div class="max-w-2xl">
    <h1 class="heading-1 mb-8">Edit Account</h1>
    <div class="form-card">
        <form action="{{ route('web.accounts.update', $account) }}" method="POST" class="flex flex-col gap-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-5">
                <div class="form-group">
                    <label for="name" class="form-label">Name *</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $account->name) }}" required class="input">
                </div>
                <div class="form-group">
                    <label for="code" class="form-label">Code *</label>
                    <input id="code" type="text" name="code" value="{{ old('code', $account->code) }}" required class="input">
                </div>
            </div>
            <div class="form-group">
                <label for="type" class="form-label">Type *</label>
                <select name="type" id="type" required class="select">
                    @foreach(['asset','liability','equity','revenue','expense'] as $t)
                        <option value="{{ $t }}" {{ old('type', $account->type) == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $account->is_active) ? 'checked' : '' }} class="checkbox">
                <span class="body">Active</span>
            </label>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Update Account</button>
                <a href="{{ route('web.accounts.index') }}" class="btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

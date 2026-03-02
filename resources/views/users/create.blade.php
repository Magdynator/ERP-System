@extends('layouts.app')
@section('title', 'Provision Identity')

@section('content')
<a href="{{ route('web.users.index') }}" class="link-back reveal">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    Identity Matrix
</a>

<div class="max-w-2xl mt-8">
    <div class="mb-8 reveal">
        <h1 class="heading-1">Create User</h1>
        <p class="text-sm text-gray-400 font-bold uppercase tracking-widest mt-1">Configure New Access Point</p>
    </div>

    <form action="{{ route('web.users.store') }}" method="POST" class="form-card reveal">
        @csrf
        
        <div class="form-group mb-6">
            <label for="name" class="form-label">Personnel Designation</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus class="input">
            @error('name')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group mb-6">
            <label for="email" class="form-label">Network ID</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="input">
            @error('email')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group mb-6">
            <label for="role" class="form-label">Access Level (Role)</label>
            <select name="role" id="role" required class="select">
                <option value="">Select Level</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                        {{ strtoupper(str_replace('_', ' ', $role->name)) }}
                    </option>
                @endforeach
            </select>
            @error('role')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group mb-6">
            <label for="password" class="form-label">Security Key</label>
            <input type="password" name="password" id="password" required class="input">
            @error('password')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group mb-8">
            <label for="password_confirmation" class="form-label">Verify Key</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required class="input">
            @error('password_confirmation')<span class="form-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-actions border-t border-gray-100/30 pt-6 mt-6 flex justify-end">
            <button type="submit" class="btn-primary">Provision Access</button>
        </div>
    </form>
</div>
@endsection

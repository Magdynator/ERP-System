@extends('layouts.app')
@section('title', 'Identity Provisioning')
@section('content')

<div class="space-y-10">
    <form method="POST" action="{{ route('register') }}" class="space-y-8">
        @csrf
        
        <div class="form-group reveal">
            <label for="name" class="form-label">Personnel Designation (Name)</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="input @error('name') ring-rose-500 @enderror" placeholder="John Doe">
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group reveal">
            <label for="email" class="form-label">Network ID (Email)</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="input @error('email') ring-rose-500 @enderror" placeholder="operator@core.system">
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group reveal">
            <label for="password" class="form-label">Security Key</label>
            <input id="password" type="password" name="password" required class="input @error('password') ring-rose-500 @enderror" placeholder="••••••••">
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group reveal">
            <label for="password_confirmation" class="form-label">Verify Key</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="input" placeholder="••••••••">
        </div>

        <div class="pt-2 reveal">
            <button type="submit" class="btn-primary w-full py-5 text-xs uppercase tracking-[0.25em] font-black shadow-2xl shadow-brand-500/20">
                Execute Provisioning
            </button>
        </div>
    </form>

    <div class="text-center pt-8 border-t border-gray-100/30 reveal">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
            Identity already exists? 
            <a href="{{ route('login') }}" class="text-brand-500 hover:text-brand-700 font-black ml-2 transition-colors">Resume Session</a>
        </p>
    </div>
</div>
@endsection

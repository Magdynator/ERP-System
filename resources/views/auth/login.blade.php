@extends('layouts.app')
@section('title', 'System Access')
@section('content')

<div class="space-y-10">
    <form method="POST" action="{{ route('login') }}" class="space-y-8">
        @csrf
        
        <div class="form-group reveal">
            <label for="email" class="form-label">Identity Credential (Email)</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="input @error('email') ring-rose-500 @enderror" placeholder="operator@core.system">
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        
        <div class="form-group reveal">
            <div class="flex items-center justify-between px-1">
                <label for="password" class="form-label">Security Key</label>
                <a href="#" class="text-[10px] font-black text-brand-500 hover:text-brand-700 uppercase tracking-widest transition-all">Reset Key</a>
            </div>
            <input id="password" type="password" name="password" required class="input @error('password') ring-rose-500 @enderror" placeholder="••••••••">
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>
 
        <div class="flex items-center reveal">
            <label class="flex items-center gap-3 cursor-pointer group">
                <input id="remember" type="checkbox" name="remember" class="checkbox">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest group-hover:text-gray-600 transition-colors">Persistent Session</span>
            </label>
        </div>
 
        <div class="pt-2 reveal">
            <button type="submit" class="btn-primary w-full py-5 text-xs uppercase tracking-[0.25em] font-black shadow-2xl shadow-brand-500/20">
                Authenticate Access
            </button>
        </div>
    </form>
 
</div>
@endsection

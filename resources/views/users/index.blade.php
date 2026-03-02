@extends('layouts.app')
@section('title', 'Manage Users')

@section('content')
<div class="page-header reveal">
    <div>
        <h1 class="heading-1">Users</h1>
        <p class="text-sm text-gray-400 font-bold uppercase tracking-widest mt-1">System Provisioning</p>
    </div>
    <a href="{{ route('web.users.create') }}" class="btn-primary">
        <svg class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Provision User
    </a>
</div>

<div class="table-card reveal mt-8">
    <div class="px-8 py-5 border-b border-gray-100/30">
        <h2 class="heading-3">Authorized Personnel</h2>
    </div>
    <table class="table-modern">
        <thead>
            <tr>
                <th>Identity</th>
                <th>Network ID</th>
                <th>Access Level (Role)</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="group hover:bg-white/40 transition-all">
                <td class="font-black text-gray-900 tracking-tighter">{{ $user->name }}</td>
                <td class="font-mono text-xs text-gray-500">{{ $user->email }}</td>
                <td>
                    <span class="status-badge status-neutral">{{ strtoupper(str_replace('_', ' ', $user->role)) }}</span>
                </td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('web.users.edit', $user) }}" class="text-brand-500 hover:text-brand-700 font-bold text-sm uppercase tracking-widest transition-colors">Edit</a>
                        @if(auth()->id() !== $user->id)
                            <form action="{{ route('web.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Revoke access for this user?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold text-sm uppercase tracking-widest transition-colors">Revoke</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="py-12 text-center text-gray-400 font-bold uppercase tracking-widest">No matching records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($users->hasPages())
        <div class="px-8 py-4 border-t border-gray-100/30">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection

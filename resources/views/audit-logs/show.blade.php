@extends('layouts.app')
@section('title', 'Log Analysis')

@section('content')
<div class="page-header reveal">
    <div class="page-title-section">
        <div class="flex items-center gap-4 mb-3">
            <a href="{{ route('web.audit-logs.index') }}" class="w-10 h-10 rounded-2xl bg-white/50 backdrop-blur-md flex items-center justify-center text-gray-400 hover:text-brand-500 hover:shadow-lg hover:scale-110 active:scale-95 transition-all outline-none">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <h1 class="heading-1">Audit Log Entry #{{ $auditLog->id }}</h1>
        </div>
        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest px-1">Detailed diagnostic of recorded system behavior</p>
    </div>
</div>

@php
   $oldIP = $auditLog->old_values['_ip_address'] ?? null;
   $newIP = $auditLog->new_values['_ip_address'] ?? null;
   $ip = $auditLog->ip_address ?? $newIP ?? $oldIP ?? 'N/A';
   
   $oldVals = $auditLog->old_values;
   $newVals = $auditLog->new_values;
   if(isset($oldVals['_ip_address'])) unset($oldVals['_ip_address']);
   if(isset($newVals['_ip_address'])) unset($newVals['_ip_address']);
@endphp

<div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
    {{-- Diagnostic Panel --}}
    <div class="xl:col-span-5 flex flex-col gap-6 reveal">
        <div class="card p-8">
            <h2 class="text-[10px] font-black uppercase tracking-[0.3em] text-brand-600 mb-8 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-brand-500 rounded-full"></span> Core Intelligence
            </h2>
            
            <dl class="space-y-8">
                <div class="flex flex-col gap-1.5 px-1">
                    <dt class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Temporal Stamp</dt>
                    <dd class="text-lg font-black text-gray-900 tracking-tighter">{{ $auditLog->created_at->format('M d, Y @ H:i:s') }}</dd>
                </div>

                <div class="flex flex-col gap-1.5 px-1">
                    <dt class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Active Personnel</dt>
                    <dd class="text-md font-bold text-gray-800 flex items-center gap-2">
                         <div class="w-7 h-7 rounded-lg bg-gray-900 text-white flex items-center justify-center text-[10px] font-black">
                            {{ strtoupper(substr($auditLog->user->name ?? 'S', 0, 1)) }}
                        </div>
                        {{ $auditLog->user ? $auditLog->user->name : 'Autonomous System' }}
                    </dd>
                </div>

                <div class="flex flex-col gap-1.5 px-1">
                    <dt class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Source Protocol (IP)</dt>
                    <dd class="font-mono text-xs text-brand-600 font-bold bg-brand-50 px-3 py-1.5 rounded-xl border border-brand-100 self-start">{{ $ip }}</dd>
                </div>

                <div class="flex flex-col gap-1.5 px-1">
                    <dt class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Operational Event</dt>
                    <dd>
                        <span class="status-badge bg-gray-900 text-white shadow-xl shadow-gray-900/20 px-4 py-2 text-xs font-black uppercase tracking-[0.2em]">
                            {{ $auditLog->action }}
                        </span>
                    </dd>
                </div>

                <div class="flex flex-col gap-1.5 px-1">
                    <dt class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Target Node Class</dt>
                    <dd class="text-sm font-bold text-gray-500 italic">{{ $auditLog->auditable_type }} <span class="bg-gray-100 text-gray-400 not-italic px-2 py-0.5 rounded-lg ml-2 font-black">#{{ $auditLog->auditable_id }}</span></dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Delta Visualization --}}
    <div class="xl:col-span-7 reveal">
        <div class="glass-card-dark flex flex-col min-h-[500px] border-white/5">
            <div class="px-8 py-6 border-b border-white/5 flex items-center justify-between">
                <h2 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-white/20 rounded-full"></span> Data Structure Delta
                </h2>
                <div class="text-[9px] font-black text-emerald-400 uppercase tracking-widest bg-emerald-400/10 px-3 py-1 rounded-full border border-emerald-400/20 animate-pulse">
                    Encrypted Stream
                </div>
            </div>
            
            <div class="p-8 overflow-x-auto space-y-10 custom-scrollbar">
                @if(!empty($oldVals))
                <div class="reveal">
                    <h3 class="text-[10px] font-black text-rose-400 mb-3 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500 shadow-lg shadow-rose-500/50"></span> Terminal State (Old)
                    </h3>
                    <div class="rounded-3xl bg-rose-500/5 border border-rose-500/10 p-6 relative group">
                        <pre class="text-xs font-mono text-rose-200/80 leading-relaxed font-medium"><code>{{ json_encode($oldVals, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        <div class="absolute top-4 right-4 text-[9px] font-black text-rose-500/40 uppercase tracking-widest group-hover:text-rose-500 transition-colors">V_PREV</div>
                    </div>
                </div>
                @endif

                @if(!empty($newVals))
                <div class="reveal">
                    <h3 class="text-[10px] font-black text-emerald-400 mb-3 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-lg shadow-emerald-500/50"></span> New Commitment State
                    </h3>
                    <div class="rounded-3xl bg-emerald-500/5 border border-emerald-500/10 p-6 relative group">
                        <pre class="text-xs font-mono text-emerald-200/80 leading-relaxed font-medium"><code>{{ json_encode($newVals, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        <div class="absolute top-4 right-4 text-[9px] font-black text-emerald-500/40 uppercase tracking-widest group-hover:text-emerald-500 transition-colors">V_NEXT</div>
                    </div>
                </div>
                @endif

                @if(empty($oldVals) && empty($newVals))
                <div class="flex flex-col items-center justify-center h-full py-20 text-gray-500 opacity-30 select-none reveal">
                    <svg class="w-20 h-20 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <span class="text-xs font-black uppercase tracking-[0.5em]">No Data Delta Recorded</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

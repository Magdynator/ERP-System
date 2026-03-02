@extends('layouts.app')
@section('title', 'New Journal Entry')
@section('nav-context', 'Journal Entries')
@section('content')

<a href="{{ route('web.journal-entries.index') }}" class="link-back">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    Journal Entries
</a>

<div class="max-w-2xl">
    <h1 class="heading-1 mb-8">Create Journal Entry</h1>
    <div class="form-card">
        <form action="{{ route('web.journal-entries.store') }}" method="POST" class="flex flex-col gap-6" id="journal-form">
            @csrf
            <div class="form-group">
                <label for="description" class="form-label">Description *</label>
                <input id="description" type="text" name="description" value="{{ old('description') }}" required class="input @error('description') input-error @enderror">
                @error('description')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Lines (debits must equal credits)</label>
                <div id="lines" class="flex flex-col gap-3">
                    @for($i = 0; $i < max(2, (int) old('lines_count', 2)); $i++)
                    <div class="flex gap-3 items-center flex-wrap p-3 rounded-xl bg-gray-50 border border-gray-100">
                        <select name="lines[{{ $i }}][account_id]" class="select flex-1 min-w-[200px] border-white shadow-none" required>
                            <option value="">Account</option>
                            @foreach($accounts as $a)
                                <option value="{{ $a->id }}" {{ old("lines.{$i}.account_id") == $a->id ? 'selected' : '' }}>{{ $a->code }} — {{ $a->name }}</option>
                            @endforeach
                        </select>
                        <input type="number" step="0.01" name="lines[{{ $i }}][debit]" value="{{ old("lines.{$i}.debit") }}" placeholder="Debit" class="input w-32 border-white shadow-none" min="0">
                        <span class="text-gray-300 font-medium">/</span>
                        <input type="number" step="0.01" name="lines[{{ $i }}][credit]" value="{{ old("lines.{$i}.credit") }}" placeholder="Credit" class="input w-32 border-white shadow-none" min="0">
                    </div>
                    @endfor
                </div>
                <button type="button" onclick="addLine()" class="link mt-3 text-sm inline-flex items-center gap-1 w-fit">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Add line
                </button>
            </div>
            <div class="form-group">
                <label for="currency" class="form-label">Currency</label>
                <input id="currency" type="text" name="currency" value="{{ old('currency', 'USD') }}" maxlength="3" class="input w-28 uppercase text-sm font-semibold tracking-wider">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Create Entry</button>
                <a href="{{ route('web.journal-entries.index') }}" class="btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
<template id="line-tpl">
    <div class="flex gap-3 items-center flex-wrap p-3 rounded-xl bg-gray-50 border border-gray-100">
        <select name="lines[__INDEX__][account_id]" class="select flex-1 min-w-[200px] border-white shadow-none" required><option value="">Account</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} — {{ $a->name }}</option>@endforeach</select>
        <input type="number" step="0.01" name="lines[__INDEX__][debit]" placeholder="Debit" class="input w-32 border-white shadow-none" min="0">
        <span class="text-gray-300 font-medium">/</span>
        <input type="number" step="0.01" name="lines[__INDEX__][credit]" placeholder="Credit" class="input w-32 border-white shadow-none" min="0">
    </div>
</template>
<script>
let lineIndex = {{ max(2, (int) old('lines_count', 2)) }};
function addLine() {
    const tpl = document.getElementById('line-tpl').innerHTML.replace(/__INDEX__/g, lineIndex);
    document.getElementById('lines').insertAdjacentHTML('beforeend', tpl);
    lineIndex++;
}
</script>
@endsection

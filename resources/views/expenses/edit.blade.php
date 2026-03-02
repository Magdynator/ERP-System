@extends('layouts.app')
@section('title', 'Edit Expense')
@section('nav-context', 'Expenses')
@section('content')

<a href="{{ route('web.expenses.index') }}" class="link-back">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    Expenses
</a>

<div class="max-w-2xl">
    <h1 class="heading-1 mb-8">Edit Expense</h1>
    <div class="form-card">
        <form action="{{ route('web.expenses.update', $expense) }}" method="POST" class="flex flex-col gap-6">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="expense_category_id" class="form-label">Category *</label>
                <select name="expense_category_id" id="expense_category_id" required class="select">
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div class="form-group">
                    <label for="amount" class="form-label">Amount *</label>
                    <input id="amount" type="number" step="0.01" name="amount" value="{{ old('amount', $expense->amount) }}" required min="0" class="input">
                </div>
                <div class="form-group">
                    <label for="expense_date" class="form-label">Date *</label>
                    <input id="expense_date" type="date" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required class="input">
                </div>
            </div>
            <div class="form-group">
                <label for="vendor_name" class="form-label">Vendor name</label>
                <input id="vendor_name" type="text" name="vendor_name" value="{{ old('vendor_name', $expense->vendor_name) }}" class="input">
            </div>
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" rows="2" class="textarea">{{ old('description', $expense->description) }}</textarea>
            </div>
            <div class="form-group">
                <label for="currency" class="form-label">Currency</label>
                <input id="currency" type="text" name="currency" value="{{ old('currency', $expense->currency) }}" maxlength="3" class="input w-28">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Update Expense</button>
                <a href="{{ route('web.expenses.index') }}" class="btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

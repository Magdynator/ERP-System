@extends('layouts.app')
@section('title', 'New Refund')
@section('nav-context', 'Refunds')
@section('content')
<a href="{{ route('web.refunds.index') }}" class="link-back">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    Back to Refunds
</a>

<div class="max-w-2xl">
    <h1 class="heading-1 mb-8">Create Refund</h1>
    
    @if (session('error'))
        <div class="alert-error mb-8">{{ session('error') }}</div>
    @endif
    
    <div class="form-card">
        <form action="{{ route('web.refunds.store') }}" method="POST" class="flex flex-col gap-6" id="refund-form">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="form-group">
                    <label for="sale-select" class="form-label">Sale *</label>
                    <select name="sale_id" id="sale-select" required class="select @error('sale_id') input-error @enderror">
                        <option value="">Select sale</option>
                        @foreach($sales as $s)
                            <option value="{{ $s->id }}" {{ old('sale_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->sale_number }} — {{ $s->sale_date->format('M d, Y') }} ({{ number_format($s->total, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('sale_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                
                <div class="form-group">
                    <label for="warehouse_id" class="form-label">Warehouse (Return Location) *</label>
                    <select name="warehouse_id" id="warehouse_id" required class="select">
                        <option value="">Select warehouse</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-section">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="heading-3">Items to Refund</h3>
                    <p class="text-xs text-gray-500 font-medium bg-gray-100 px-2.5 py-1 rounded-full">From selected sale</p>
                </div>
                
                <div id="refund-items" class="flex flex-col gap-3">
                    <div class="p-6 text-center border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                        <svg class="mx-auto h-8 w-8 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        <p class="text-sm text-gray-500">Select a sale first, then enter quantities to refund.</p>
                    </div>
                </div>
                <p id="refund-items-error" class="form-error hidden mt-3">Enter at least one item with quantity &gt; 0.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="form-group sm:col-span-2">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea id="notes" name="notes" rows="2" class="textarea" placeholder="Reason for refund...">{{ old('notes') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="currency" class="form-label">Currency</label>
                    <input id="currency" type="text" name="currency" value="{{ old('currency', 'USD') }}" maxlength="3" class="input uppercase tracking-widest font-semibold text-sm">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Process Refund</button>
                <a href="{{ route('web.refunds.index') }}" class="btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
class ProductQuantityManager {
    constructor(options) {
        this.container = document.getElementById(options.containerId);
        this.inputPrefix = options.inputPrefix || 'items';
        this.quantityClass = options.quantityClass || 'product-qty';
        this.storedValues = new Map(); 
    }

    createQuantityInput(product, index, existingValue = null) {
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-center gap-3 w-40 shrink-0';
        
        const input = document.createElement('input');
        input.type = 'number';
        input.step = '0.01';
        input.min = '0';
        input.max = String(product.maxQuantity || product.quantity || '999999');
        input.className = 'input !py-1.5 text-right font-medium text-sm w-full ' + this.quantityClass;
        input.id = `qty-${product.id}-${index}`;
        input.name = `${this.inputPrefix}[${index}][quantity]`;
        input.placeholder = '0.00';
        
        if (existingValue !== null && existingValue !== undefined && existingValue !== '') {
            input.value = parseFloat(existingValue).toFixed(2);
        }
        
        input.addEventListener('input', (e) => this.validateQuantityInput(e.target, product));
        input.addEventListener('blur', (e) => this.validateQuantityInput(e.target, product));
        
        wrapper.appendChild(input);
        return { wrapper, input };
    }

    validateQuantityInput(input, product) {
        const value = parseFloat(input.value || 0);
        const min = parseFloat(input.min || 0);
        const max = parseFloat(input.max || 999999);
        
        input.classList.remove('ring-red-300', 'focus:ring-red-500', 'bg-red-50/50');
        
        if (input.value !== '' && (value < min || value > max)) {
            input.classList.add('ring-red-300', 'focus:ring-red-500', 'bg-red-50/50');
            return false;
        }
        return true;
    }

    createProductRow(product, index, existingQuantity = null) {
        const rowDiv = document.createElement('div');
        rowDiv.className = 'flex gap-4 items-center p-3 sm:px-4 rounded-xl bg-gray-50 border border-gray-100 transition-colors hover:bg-gray-100/50';
        rowDiv.setAttribute('data-product-id', product.id);
        rowDiv.setAttribute('data-index', index);
        
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `${this.inputPrefix}[${index}][sale_item_id]`;
        hiddenInput.value = product.id;
        
        const productSection = document.createElement('div');
        productSection.className = 'flex-1 min-w-0';
        
        const productLabel = document.createElement('label');
        productLabel.className = 'block truncate text-sm font-semibold text-gray-800';
        
        const maxLabel = document.createElement('p');
        maxLabel.className = 'text-xs text-gray-500 mt-0.5';
        maxLabel.textContent = `Purchased: ${product.quantity || product.maxQuantity || 'N/A'}`;
        
        productSection.appendChild(productLabel);
        productSection.appendChild(maxLabel);
        
        const { wrapper: qtyWrapper } = this.createQuantityInput(product, index, existingQuantity);
        
        rowDiv.appendChild(hiddenInput);
        rowDiv.appendChild(productSection);
        rowDiv.appendChild(qtyWrapper);
        
        return rowDiv;
    }

    addProduct(product, existingQuantity = null) {
        if (!this.container) return;
        const index = this.container.children.length;
        const row = this.createProductRow(product, index, existingQuantity);
        this.container.appendChild(row);
        return row;
    }

    clear() {
        if (this.container) {
            this.container.innerHTML = '';
            this.storedValues.clear();
        }
    }

    renderProducts(products, existingQuantities = {}) {
        this.clear();
        if (!products || products.length === 0) {
            this.container.innerHTML = `<div class="p-6 text-center border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                <p class="text-sm text-gray-500">No items found for this sale.</p>
            </div>`;
            return;
        }
        products.forEach((product, index) => {
            const existingQty = existingQuantities[product.id] || null;
            this.addProduct(product, existingQty);
        });
    }

    getQuantities() {
        const quantities = {};
        const inputs = this.container.querySelectorAll('.' + this.quantityClass);
        inputs.forEach(input => {
            const row = input.closest('[data-product-id]');
            if (row) {
                const productId = row.getAttribute('data-product-id');
                quantities[productId] = parseFloat(input.value || 0);
            }
        });
        return quantities;
    }

    validateAll() {
        const inputs = this.container.querySelectorAll('.' + this.quantityClass);
        let isValid = true;
        inputs.forEach(input => {
            const row = input.closest('[data-product-id]');
            if (row) {
                const maxQty = parseFloat(input.max || 999999);
                if (!this.validateQuantityInput(input, { maxQuantity: maxQty })) {
                    isValid = false;
                }
            }
        });
        return isValid;
    }
}

(function() {
    'use strict';
    
    // PHP variables are securely handled via JSON encoding
    const salesData = @json($salesData ?? []);
    const saleSelect = document.getElementById('sale-select');
    const container = document.getElementById('refund-items');
    
    const productManager = new ProductQuantityManager({
        containerId: 'refund-items',
        inputPrefix: 'items',
        quantityClass: 'refund-qty'
    });
    
    function renderItems(saleId) {
        const saleIdKey = String(saleId);
        const sale = salesData[saleIdKey] || salesData[saleId];
        
        if (!saleId || !sale || !sale.items || sale.items.length === 0) {
            productManager.clear();
            container.innerHTML = `<div class="p-6 text-center border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                <svg class="mx-auto h-8 w-8 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                <p class="text-sm text-gray-500">Select a sale first, then enter quantities to refund.</p>
            </div>`;
            return;
        }
        
        const products = sale.items.map(item => ({
            id: item.id,
            product_name: item.product_name,
            quantity: item.quantity,
            maxQuantity: item.quantity
        }));
        
        productManager.renderProducts(products);
    }
    
    if (saleSelect) {
        saleSelect.addEventListener('change', function() {
            renderItems(this.value);
        });
        
        if (saleSelect.value) {
            renderItems(saleSelect.value);
        }
    }
    
    const refundForm = document.getElementById('refund-form');
    if (refundForm) {
        refundForm.addEventListener('submit', function(e) {
            if (!productManager.validateAll()) {
                e.preventDefault();
                const errorMsg = document.getElementById('refund-items-error');
                if (errorMsg) {
                    errorMsg.textContent = 'Please fix invalid quantity values.';
                    errorMsg.classList.remove('hidden');
                }
                return false;
            }
            
            const quantities = productManager.getQuantities();
            const hasQty = Object.values(quantities).some(qty => qty > 0);
            
            if (!hasQty) {
                e.preventDefault();
                const errorMsg = document.getElementById('refund-items-error');
                if (errorMsg) {
                    errorMsg.textContent = 'Enter at least one item with quantity > 0.';
                    errorMsg.classList.remove('hidden');
                }
                return false;
            }
            
            const errorMsg = document.getElementById('refund-items-error');
            if (errorMsg) {
                errorMsg.classList.add('hidden');
            }
        });
    }
})();
</script>
@endsection

{{-- 
    Example: Product Quantity Manager Component
    This demonstrates how to use the ProductQuantityManager class
--}}
<!DOCTYPE html>
<html>
<head>
    <title>Product Quantity Manager Example</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Product Quantity Manager Example</h1>
        
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-lg font-semibold mb-4">Add Products</h2>
            <div class="flex gap-2 mb-4">
                <button id="add-product-1" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Add Product 1
                </button>
                <button id="add-product-2" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Add Product 2
                </button>
                <button id="add-product-3" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Add Product 3
                </button>
                <button id="clear-all" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Clear All
                </button>
            </div>
            
            <div id="products-container" class="space-y-2 border-2 border-dashed border-gray-300 p-4 rounded min-h-[200px]">
                <p class="text-gray-500 text-sm">Products will appear here...</p>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Current Quantities</h2>
            <pre id="quantities-display" class="bg-gray-100 p-4 rounded text-sm"></pre>
            <button id="validate-all" class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Validate All
            </button>
        </div>
    </div>

    <script>
    /**
     * ProductQuantityManager - Reusable component for managing product quantity inputs
     */
    class ProductQuantityManager {
        constructor(options) {
            this.container = document.getElementById(options.containerId);
            this.inputPrefix = options.inputPrefix || 'items';
            this.quantityClass = options.quantityClass || 'product-qty';
            this.onQuantityChange = options.onQuantityChange || null;
            this.storedValues = new Map();
        }

        createQuantityInput(product, index, existingValue = null) {
            const wrapper = document.createElement('div');
            wrapper.className = 'flex items-center gap-2';
            
            const label = document.createElement('label');
            label.className = 'text-sm text-gray-600 whitespace-nowrap';
            label.textContent = 'Quantity:';
            label.setAttribute('for', `qty-${product.id}-${index}`);
            
            const input = document.createElement('input');
            input.type = 'number';
            input.step = '0.01';
            input.min = '0';
            input.max = String(product.maxQuantity || product.quantity || '999999');
            input.className = 'rounded border border-gray-300 px-3 py-2 w-32 ' + this.quantityClass + 
                             ' focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent' +
                             ' hover:border-gray-400 transition-colors';
            input.id = `qty-${product.id}-${index}`;
            input.name = `${this.inputPrefix}[${index}][quantity]`;
            input.placeholder = '0.00';
            
            if (existingValue !== null && existingValue !== undefined && existingValue !== '') {
                input.value = parseFloat(existingValue).toFixed(2);
            }
            
            const storageKey = `${product.id}-${index}`;
            this.storedValues.set(storageKey, input.value);
            
            input.addEventListener('input', (e) => {
                this.validateQuantityInput(e.target, product);
                this.storedValues.set(storageKey, e.target.value);
                if (this.onQuantityChange) {
                    this.onQuantityChange(product, e.target.value, index);
                }
            });
            
            input.addEventListener('blur', (e) => {
                this.validateQuantityInput(e.target, product);
            });
            
            wrapper.appendChild(label);
            wrapper.appendChild(input);
            
            return { wrapper, input };
        }

        validateQuantityInput(input, product) {
            const value = parseFloat(input.value || 0);
            const min = parseFloat(input.min || 0);
            const max = parseFloat(input.max || 999999);
            
            input.classList.remove('border-red-500', 'bg-red-50');
            
            if (input.value !== '' && (value < min || value > max)) {
                input.classList.add('border-red-500', 'bg-red-50');
                input.title = `Value must be between ${min} and ${max}`;
                return false;
            } else {
                input.title = '';
                return true;
            }
        }

        createProductRow(product, index, existingQuantity = null) {
            const rowDiv = document.createElement('div');
            rowDiv.className = 'flex gap-3 items-center p-3 border border-gray-200 rounded bg-white hover:border-gray-300 transition-colors';
            rowDiv.setAttribute('data-product-id', product.id);
            rowDiv.setAttribute('data-index', index);
            
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `${this.inputPrefix}[${index}][product_id]`;
            hiddenInput.value = product.id;
            
            const productSection = document.createElement('div');
            productSection.className = 'flex-1 min-w-0';
            
            const productLabel = document.createElement('label');
            productLabel.className = 'text-sm font-medium text-gray-700 block truncate';
            productLabel.textContent = product.product_name || product.name || `Product #${product.id}`;
            
            const maxLabel = document.createElement('span');
            maxLabel.className = 'text-xs text-gray-500 ml-2';
            maxLabel.textContent = `(max: ${product.quantity || product.maxQuantity || 'N/A'})`;
            
            productLabel.appendChild(maxLabel);
            productSection.appendChild(productLabel);
            
            const { wrapper: qtyWrapper, input: qtyInput } = this.createQuantityInput(product, index, existingQuantity);
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'px-2 py-1 text-red-600 hover:text-red-800 text-sm';
            removeBtn.textContent = 'Remove';
            removeBtn.onclick = () => this.removeProduct(product.id);
            
            rowDiv.appendChild(hiddenInput);
            rowDiv.appendChild(productSection);
            rowDiv.appendChild(qtyWrapper);
            rowDiv.appendChild(removeBtn);
            
            return rowDiv;
        }

        addProduct(product, existingQuantity = null) {
            if (!this.container) return;
            
            const index = this.container.children.length;
            const row = this.createProductRow(product, index, existingQuantity);
            this.container.appendChild(row);
            
            return row;
        }

        updateProduct(productId, newProductData, newQuantity = null) {
            const row = this.container.querySelector(`[data-product-id="${productId}"]`);
            if (!row) {
                return this.addProduct(newProductData, newQuantity);
            }
            
            const index = row.getAttribute('data-index');
            const qtyInput = row.querySelector('.' + this.quantityClass);
            
            if (qtyInput && newQuantity !== null) {
                qtyInput.value = parseFloat(newQuantity).toFixed(2);
                this.storedValues.set(`${productId}-${index}`, qtyInput.value);
            }
            
            const productLabel = row.querySelector('label');
            if (productLabel && newProductData.product_name) {
                productLabel.textContent = newProductData.product_name;
            }
            
            return row;
        }

        removeProduct(productId) {
            const row = this.container.querySelector(`[data-product-id="${productId}"]`);
            if (row) {
                row.remove();
                this.reindexRows();
            }
        }

        reindexRows() {
            const rows = Array.from(this.container.children);
            rows.forEach((row, index) => {
                row.setAttribute('data-index', index);
                const hiddenInput = row.querySelector('input[type="hidden"]');
                const qtyInput = row.querySelector('.' + this.quantityClass);
                
                if (hiddenInput) {
                    hiddenInput.name = `${this.inputPrefix}[${index}][product_id]`;
                }
                if (qtyInput) {
                    qtyInput.name = `${this.inputPrefix}[${index}][quantity]`;
                }
            });
        }

        clear() {
            if (this.container) {
                this.container.innerHTML = '<p class="text-gray-500 text-sm">Products will appear here...</p>';
                this.storedValues.clear();
            }
        }

        renderProducts(products, existingQuantities = {}) {
            this.clear();
            
            if (!products || products.length === 0) {
                this.container.innerHTML = '<p class="text-gray-500 text-sm p-3">No products found.</p>';
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
                    const productId = row.getAttribute('data-product-id');
                    const maxQty = parseFloat(input.max || 999999);
                    const product = { id: productId, maxQuantity: maxQty };
                    if (!this.validateQuantityInput(input, product)) {
                        isValid = false;
                    }
                }
            });
            
            return isValid;
        }
    }

    // Example usage
    const productManager = new ProductQuantityManager({
        containerId: 'products-container',
        inputPrefix: 'products',
        quantityClass: 'product-qty',
        onQuantityChange: function(product, quantity, index) {
            updateQuantitiesDisplay();
        }
    });

    // Sample products
    const sampleProducts = {
        1: { id: 1, product_name: 'Laptop', quantity: 10, maxQuantity: 10 },
        2: { id: 2, product_name: 'Mouse', quantity: 50, maxQuantity: 50 },
        3: { id: 3, product_name: 'Keyboard', quantity: 25, maxQuantity: 25 }
    };

    function updateQuantitiesDisplay() {
        const quantities = productManager.getQuantities();
        document.getElementById('quantities-display').textContent = JSON.stringify(quantities, null, 2);
    }

    document.getElementById('add-product-1').addEventListener('click', () => {
        productManager.addProduct(sampleProducts[1]);
        updateQuantitiesDisplay();
    });

    document.getElementById('add-product-2').addEventListener('click', () => {
        productManager.addProduct(sampleProducts[2]);
        updateQuantitiesDisplay();
    });

    document.getElementById('add-product-3').addEventListener('click', () => {
        productManager.addProduct(sampleProducts[3]);
        updateQuantitiesDisplay();
    });

    document.getElementById('clear-all').addEventListener('click', () => {
        productManager.clear();
        updateQuantitiesDisplay();
    });

    document.getElementById('validate-all').addEventListener('click', () => {
        const isValid = productManager.validateAll();
        alert(isValid ? 'All quantities are valid!' : 'Some quantities are invalid. Check red highlighted fields.');
    });

    // Initial display
    updateQuantitiesDisplay();
    </script>
</body>
</html>

# ERP System Architecture Documentation

## Table of Contents

1. [Overall Architecture](#1-overall-architecture)
2. [Project Structure](#2-project-structure)
3. [Package Analysis](#3-package-analysis)
4. [Inter-Package Communication](#4-inter-package-communication)
5. [Business Flows](#5-business-flows)
6. [Double-Entry Accounting](#6-double-entry-accounting)
7. [Refund Processing](#7-refund-processing)
8. [Profit Calculation](#8-profit-calculation)
9. [Design Patterns](#9-design-patterns)
10. [Multi-Tenancy Support](#10-multi-tenancy-support)

---

## 1. Overall Architecture

### Architecture Pattern: **Modular Monolith with Package-Based Structure**

This ERP system follows a **modular monolith** pattern using Laravel's package architecture. Packages are organized as independent modules within a single codebase, communicating through well-defined contracts (interfaces).

### Key Characteristics

- **Single Codebase**: All packages live in `packages/` directory
- **Composer Path Repositories**: Packages registered as local Composer dependencies
- **Service Provider Discovery**: Laravel auto-discovers package service providers
- **Contract-Based Communication**: Packages communicate via interfaces, not direct model access
- **Independent Migrations**: Each package manages its own database migrations
- **Shared Foundation**: Core package provides base models and utilities

### Package Discovery & Registration

**Root `composer.json` Configuration:**
```json
{
  "repositories": [
    {"type": "path", "url": "packages/Core"},
    {"type": "path", "url": "packages/Products"},
    {"type": "path", "url": "packages/Inventory"},
    {"type": "path", "url": "packages/Sales"},
    {"type": "path", "url": "packages/Accounting"},
    {"type": "path", "url": "packages/Expenses"},
    {"type": "path", "url": "packages/Refunds"}
  ],
  "require": {
    "erp/core": "@dev",
    "erp/products": "@dev",
    "erp/inventory": "@dev",
    "erp/sales": "@dev",
    "erp/accounting": "@dev",
    "erp/expenses": "@dev",
    "erp/refunds": "@dev"
  }
}
```

**Service Provider Auto-Discovery:**
- Each package declares its ServiceProvider in `composer.json` under `extra.laravel.providers`
- Laravel discovers providers via `php artisan package:discover` (runs on `composer dump-autoload`)
- ServiceProviders register routes, migrations, config, and service bindings

### Dependency Graph

```
Core (foundation - no dependencies)
├── Products (depends on Core)
├── Accounting (depends on Core)
├── Inventory (depends on Core, Products)
├── Expenses (depends on Core, Accounting)
├── Sales (depends on Core, Products, Inventory, Accounting)
└── Refunds (depends on Core, Products, Inventory, Accounting, Sales)
```

**Why This Structure?**
- **Core**: Base foundation with shared models (`BaseModel`), user management, audit logging
- **Products**: Independent product catalog (no business logic dependencies)
- **Accounting**: Independent accounting system (can be used standalone)
- **Inventory**: Depends on Products (needs product references)
- **Sales**: Orchestrates Products + Inventory + Accounting
- **Expenses**: Uses Accounting for expense entries
- **Refunds**: Reverses Sales operations, needs all dependencies

---

## 2. Project Structure

### Directory Layout

```
mainapp/
├── app/                          # Main application code
│   ├── Http/
│   │   └── Controllers/
│   │       └── Web/              # Web controllers (Blade UI)
│   └── Providers/
│       └── AppServiceProvider.php
├── packages/                     # ERP modules (packages)
│   ├── Core/                     # Foundation package
│   │   ├── src/
│   │   │   ├── Models/
│   │   │   ├── Services/
│   │   │   └── Providers/
│   │   ├── Database/
│   │   │   └── Migrations/
│   │   ├── Routes/
│   │   ├── Config/
│   │   └── composer.json
│   ├── Products/                 # Product management
│   ├── Inventory/                # Warehouse & stock
│   ├── Sales/                    # Sales orders
│   ├── Accounting/               # Double-entry accounting
│   ├── Expenses/                 # Expense tracking
│   └── Refunds/                  # Refund processing
├── routes/
│   ├── web.php                   # Web routes (Blade UI)
│   └── api.php                   # API routes (aggregates package routes)
├── resources/
│   └── views/                    # Blade templates
├── database/
│   └── seeders/                  # Database seeders
└── composer.json                 # Root composer config
```

### Package Structure (Standard)

Each package follows this structure:

```
PackageName/
├── src/
│   ├── Models/                   # Eloquent models
│   ├── Services/                 # Business logic services
│   ├── Contracts/                # Interfaces for inter-package communication
│   ├── Http/
│   │   └── Controllers/          # API controllers
│   └── Providers/
│       └── PackageServiceProvider.php
├── Database/
│   └── Migrations/               # Package migrations
├── Routes/
│   └── api.php                   # Package API routes
├── Config/
│   └── package.php               # Package configuration
└── composer.json                 # Package dependencies
```

**Why This Structure?**
- **PSR-4 Autoloading**: Standard PHP namespace-to-directory mapping
- **Separation of Concerns**: Models, services, controllers clearly separated
- **Laravel Conventions**: Follows Laravel package structure best practices
- **Independent Testing**: Each package can be tested in isolation

---

## 3. Package Analysis

### 3.1 Core Package

**Purpose:** Foundation package providing shared infrastructure

**Dependencies:** None (base package)

**Key Components:**

#### Models
- **`BaseModel`**: Abstract model extending `Illuminate\Database\Eloquent\Model`
  - Uses `SoftDeletes` trait
  - `protected $guarded = ['id']` (mass assignment protection)
  - Timestamps enabled
  - All other packages extend this model

- **`User`**: Extends Laravel's `Authenticatable`
  - Uses Spatie Permission for roles/permissions
  - Soft deletes enabled

#### Services
- **`ProfitService`**: Pure calculation service (no database access)
  - `calculateGrossProfit(array $lineItems): float`
    - Formula: `SUM((selling_price - cost_price) * quantity)`
  - `calculateNetProfit(float $grossProfit, float $totalExpenses): float`
    - Formula: `$grossProfit - $totalExpenses`

#### Database Tables
- **`audit_logs`**: Audit trail for model changes
  - Columns: `action`, `auditable_type`, `auditable_id`, `user_id`, `old_values`, `new_values`

#### Service Provider Responsibilities
- Registers config: `core.php`
- Loads migrations from `Database/Migrations`
- Sets default string length to 191 (MySQL compatibility)

**Why Core Exists:**
- **DRY Principle**: Avoids duplicating `BaseModel` across packages
- **Consistency**: Ensures all models have soft deletes and timestamps
- **Shared Utilities**: Provides common services like profit calculation
- **User Management**: Centralized authentication and authorization

---

### 3.2 Products Package

**Purpose:** Product and category management

**Dependencies:** `erp/core`

**Key Components:**

#### Models
- **`Product`**:
  - Columns: `name`, `sku` (unique), `cost_price`, `selling_price`, `tax_percentage`, `category_id` (nullable FK), `is_active`
  - Relationship: `belongsTo(Category)`
  - Soft deletes enabled

- **`Category`**:
  - Columns: `name`, `slug` (unique), `description`, `parent_id` (nullable FK), `is_active`
  - Relationships:
    - `hasMany(Product)` - Products in category
    - `belongsTo(Category)` - Parent category (hierarchical)
    - `hasMany(Category)` - Child categories
  - Soft deletes enabled

#### Services
- **`ProductService`** (implements `ProductServiceInterface`):
  - `getForSale(int $productId): ?array`
    - Returns: `['id' => int, 'cost_price' => float, 'selling_price' => float, 'tax_percentage' => float]`
    - Returns `null` if product not found or inactive
    - Used by Sales to snapshot prices at sale time

#### Contracts
- **`ProductServiceInterface`**: Defines `getForSale()` contract
- **`ProductRepositoryInterface`**: Repository pattern interface (exists but not actively used)

#### Database Tables
- **`categories`**: Hierarchical product categories
- **`products`**: Product master data with pricing

#### Service Provider Responsibilities
- Binds `ProductServiceInterface` → `ProductService`
- Registers config and migrations
- Loads routes: `api/v1/products`, `api/v1/categories`

**Why Products is Separate:**
- **Domain Separation**: Product catalog is independent business domain
- **Reusability**: Can be used by Sales, Inventory, Refunds without coupling
- **Price Snapshot**: Provides service to lock prices at sale time (prevents retroactive price changes)

---

### 3.3 Inventory Package

**Purpose:** Warehouse and stock movement management

**Dependencies:** `erp/core`, `erp/products`

**Key Components:**

#### Models
- **`Warehouse`**:
  - Columns: `name`, `code` (unique), `branch_id` (nullable), `is_active`
  - Relationship: `hasMany(StockMovement)`
  - Soft deletes enabled

- **`StockMovement`**:
  - Columns: `product_id` (FK), `warehouse_id` (FK), `quantity`, `type` (IN/OUT), `reference_type`, `reference_id`
  - Relationships:
    - `belongsTo(Warehouse)`
    - `belongsTo(Product)`
  - Constants: `TYPE_IN = 'IN'`, `TYPE_OUT = 'OUT'`
  - Methods: `isIn()`, `isOut()`
  - Soft deletes enabled

#### Services
- **`InventoryService`** (implements `InventoryServiceInterface`):

  - **`deduct(int $productId, int $warehouseId, float $quantity, string $referenceType, int $referenceId): void`**
    - Validates stock availability before deducting
    - Throws exception if insufficient stock
    - Creates OUT movement in transaction
    - Used by Sales to reduce inventory

  - **`add(int $productId, int $warehouseId, float $quantity, string $referenceType, int $referenceId): void`**
    - Validates quantity > 0
    - Creates IN movement in transaction
    - Used by Refunds to return stock

  - **`getStock(int $productId, int $warehouseId): float`**
    - Calculates: `SUM(IN) - SUM(OUT)` for product/warehouse
    - Event-sourced calculation (no balance table)

#### Contracts
- **`InventoryServiceInterface`**: Defines stock operations contract

#### Database Tables
- **`warehouses`**: Warehouse locations (can belong to branch)
- **`stock_movements`**: Event-sourced stock tracking
  - Indexes: `[product_id, warehouse_id]`, `[reference_type, reference_id]`

#### Service Provider Responsibilities
- Binds `InventoryServiceInterface` → `InventoryService`
- Registers config, migrations, routes

**Why Inventory Uses Event Sourcing:**
- **Complete Audit Trail**: Every stock change is recorded
- **Historical Reconstruction**: Can calculate stock at any point in time
- **No Synchronization Issues**: No balance table to keep in sync
- **Reference Tracking**: Links movements to sales, refunds, etc.

**Why Inventory Depends on Products:**
- Stock movements reference products (foreign key)
- Needs product existence validation

---

### 3.4 Accounting Package

**Purpose:** Double-entry accounting system

**Dependencies:** `erp/core`

**Key Components:**

#### Models
- **`Account`**:
  - Columns: `name`, `code` (unique), `type` (asset/liability/equity/revenue/expense), `branch_id` (nullable), `is_active`
  - Relationship: `hasMany(JournalLine)`
  - Method: `typeLabel(): string` - Human-readable account type
  - Soft deletes enabled

- **`JournalEntry`**:
  - Columns: `description`, `reference_type`, `reference_id`, `currency` (default 'USD'), `branch_id`, `entry_date`
  - Relationship: `hasMany(JournalLine)`
  - Accessors:
    - `getTotalDebitsAttribute(): float` - Sum of all debit lines
    - `getTotalCreditsAttribute(): float` - Sum of all credit lines
  - Soft deletes enabled

- **`JournalLine`**:
  - Columns: `journal_entry_id` (FK), `account_id` (FK), `debit`, `credit`
  - Relationships:
    - `belongsTo(JournalEntry)`
    - `belongsTo(Account)`
  - **Important**: Does NOT use `BaseModel` (extends `Model` directly)
  - **No timestamps** (immutable accounting records)
  - **No soft deletes** (accounting entries are permanent)

#### Services
- **`AccountingService`** (implements `AccountingServiceInterface`):

  - **`recordEntry(string $description, array $lines, ?string $referenceType, ?int $referenceId, ?string $currency, ?int $branchId): object`**
    - Validates debits = credits (balanced entry)
    - Throws exception if unbalanced
    - Creates entry + lines in transaction
    - Returns `JournalEntry` object

  - **`getAccountBalance(int $accountId, ?int $branchId = null): float`**
    - Returns: `SUM(debits) - SUM(credits)` for account
    - Optionally filters by `branch_id`
    - Used for financial reporting

  - **`reverseEntry(int $journalEntryId): object`**
    - Creates reversal entry (swaps debits/credits)
    - Maintains reference to original entry
    - Used for error corrections

  - **`getAccountIdByCode(string $code): ?int`**
    - Finds account by code (e.g., 'REVENUE', 'CASH', 'EXPENSE')
    - Returns `null` if not found
    - Used by Sales, Expenses, Refunds to find accounts

#### Contracts
- **`AccountingServiceInterface`**: Defines accounting operations contract

#### Database Tables
- **`accounts`**: Chart of accounts
- **`journal_entries`**: Accounting journal entries
  - Indexes: `[reference_type, reference_id]`, `entry_date`, `branch_id`
- **`journal_lines`**: Debit/credit lines for entries
  - Index: `account_id`
  - **No timestamps, no soft deletes** (immutable)

#### Service Provider Responsibilities
- Binds `AccountingServiceInterface` → `AccountingService`
- Registers config, migrations, routes: `api/v1/accounts`, `api/v1/journal-entries`

**Why Accounting is Separate:**
- **Domain Separation**: Accounting is independent business domain
- **Reusability**: Used by Sales, Expenses, Refunds
- **Double-Entry Compliance**: Ensures all entries are balanced
- **Financial Integrity**: Immutable journal lines prevent tampering

---

### 3.5 Sales Package

**Purpose:** Sales order management with inventory and accounting integration

**Dependencies:** `erp/core`, `erp/products`, `erp/inventory`, `erp/accounting`

**Key Components:**

#### Models
- **`Sale`**:
  - Columns: `sale_number` (unique), `warehouse_id` (FK), `branch_id`, `currency` (default 'USD'), `customer_name`, `customer_email`, `status`, `notes`, `sale_date`
  - Relationships:
    - `hasMany(SaleItem)` - Line items
    - `hasMany(Payment)` - Payment records
  - Accessors:
    - `getTotalAttribute(): float` - Sum of `selling_price * quantity` for all items
    - `getTotalCostAttribute(): float` - Sum of `cost_price * quantity` for all items
  - Soft deletes enabled

- **`SaleItem`**:
  - Columns: `sale_id` (FK), `product_id` (FK), `quantity`, `cost_price`, `selling_price`, `tax_percentage`
  - Relationship: `belongsTo(Sale)`
  - Accessors:
    - `getLineTotalAttribute(): float` - `selling_price * quantity`
    - `getLineCostAttribute(): float` - `cost_price * quantity`
  - **Price Snapshot**: Stores prices at sale time (not current product prices)
  - Soft deletes enabled

- **`Payment`**:
  - Columns: `sale_id` (FK), `amount`, `method`, `reference`, `paid_at`
  - Relationship: `belongsTo(Sale)`
  - Soft deletes enabled

#### Services
- **`SaleService`**: Main sales orchestration
  - **`createSale(int $warehouseId, array $items, array $payments, ...): Sale`**
    - See [Business Flows](#5-business-flows) for detailed steps
    - Orchestrates: Product lookup → Inventory deduction → Sale creation → Accounting entry

- **`SaleRefundDataService`** (implements `SaleRefundDataInterface`):
  - **`getSaleWithItemsForRefund(int $saleId): ?array`**
    - Returns sale data structure for refund processing
    - Used by Refunds package

#### Contracts
- **`SaleRefundDataInterface`**: Defines contract for refund data retrieval

#### Database Tables
- **`sales`**: Sales orders
- **`sale_items`**: Line items (with price snapshots)
- **`payments`**: Payment records

#### Service Provider Responsibilities
- Binds `SaleRefundDataInterface` → `SaleRefundDataService`
- Registers config, migrations, routes

**Why Sales Orchestrates Multiple Packages:**
- **Business Logic**: Sales is a complex business process involving multiple domains
- **Transaction Safety**: Wraps all operations in single transaction
- **Price Locking**: Snapshots prices to prevent retroactive changes
- **Integration**: Coordinates Products, Inventory, and Accounting

---

### 3.6 Expenses Package

**Purpose:** Expense tracking with accounting integration

**Dependencies:** `erp/core`, `erp/accounting`

**Key Components:**

#### Models
- **`Expense`**:
  - Columns: `expense_category_id` (FK), `amount`, `currency` (default 'USD'), `expense_date`, `vendor_name`, `vendor_reference`, `description`, `branch_id`
  - Relationship: `belongsTo(ExpenseCategory)`
  - Soft deletes enabled

- **`ExpenseCategory`**:
  - Columns: `name`, `code` (unique), `description`, `is_active`
  - Relationship: `hasMany(Expense)`
  - Soft deletes enabled

#### Services
- **`ExpenseService`**: Expense creation with accounting
  - **`createExpense(int $expenseCategoryId, float $amount, DateTimeInterface $expenseDate, ...): Expense`**
    - See [Business Flows](#5-business-flows) for detailed steps
    - Creates expense + accounting entry (Dr Expense, Cr Cash)

#### Database Tables
- **`expense_categories`**: Expense categorization
- **`expenses`**: Expense records

#### Service Provider Responsibilities
- Registers config, migrations, routes (no service binding)

**Why Expenses is Separate:**
- **Domain Separation**: Expense tracking is independent business domain
- **Accounting Integration**: Automatically creates accounting entries
- **Categorization**: Supports expense categorization for reporting

---

### 3.7 Refunds Package

**Purpose:** Refund processing with reverse accounting and stock return

**Dependencies:** `erp/core`, `erp/products`, `erp/inventory`, `erp/accounting`, `erp/sales`

**Key Components:**

#### Models
- **`Refund`**:
  - Columns: `refund_number` (unique), `sale_id` (FK), `warehouse_id` (FK), `branch_id`, `currency` (default 'USD'), `status`, `notes`, `refund_date`
  - Relationship: `hasMany(RefundItem)`
  - Accessor: `getTotalAttribute(): float` - Sum of refund item totals
  - Soft deletes enabled

- **`RefundItem`**:
  - Columns: `refund_id` (FK), `sale_item_id` (FK), `product_id` (FK), `quantity`, `cost_price`, `selling_price`
  - Relationship: `belongsTo(Refund)`
  - **Price Snapshot**: Uses original sale prices (not current product prices)
  - Soft deletes enabled

#### Services
- **`RefundService`**: Refund orchestration
  - **`createRefund(int $saleId, int $warehouseId, array $items, ...): Refund`**
    - See [Business Flows](#5-business-flows) for detailed steps
    - Orchestrates: Sale lookup → Stock return → Refund creation → Accounting reversal

#### Database Tables
- **`refunds`**: Refund records (links to original sale)
- **`refund_items`**: Line items (uses original sale prices)

#### Service Provider Responsibilities
- Registers config, migrations, routes (no service binding)

**Why Refunds is Separate:**
- **Domain Separation**: Refund processing is independent business domain
- **Reversal Logic**: Handles complex reversal of sales operations
- **Audit Trail**: Never deletes original sale (maintains history)
- **Stock Return**: Can return stock to different warehouse than sale

---

## 4. Inter-Package Communication

### 4.1 Contract/Interface Pattern

**Principle:** Packages communicate through interfaces, not direct model access.

**Why Contracts?**
- **Decoupling**: Packages depend on abstractions, not implementations
- **Testability**: Can inject mocks for testing
- **Flexibility**: Can swap implementations without changing dependent packages
- **SOLID Principles**: Follows Dependency Inversion Principle

### 4.2 Available Contracts

#### `AccountingServiceInterface` (`Erp\Accounting\Contracts`)
**Used by:** Sales, Expenses, Refunds

**Methods:**
```php
recordEntry(string $description, array $lines, ...): object
getAccountBalance(int $accountId, ?int $branchId = null): float
reverseEntry(int $journalEntryId): object
getAccountIdByCode(string $code): ?int
```

#### `InventoryServiceInterface` (`Erp\Inventory\Contracts`)
**Used by:** Sales, Refunds

**Methods:**
```php
deduct(int $productId, int $warehouseId, float $quantity, string $referenceType, int $referenceId): void
add(int $productId, int $warehouseId, float $quantity, string $referenceType, int $referenceId): void
getStock(int $productId, int $warehouseId): float
```

#### `ProductServiceInterface` (`Erp\Products\Contracts`)
**Used by:** Sales

**Methods:**
```php
getForSale(int $productId): ?array
```

#### `SaleRefundDataInterface` (`Erp\Sales\Contracts`)
**Used by:** Refunds

**Methods:**
```php
getSaleWithItemsForRefund(int $saleId): ?array
```

### 4.3 Service Binding

Services are bound in ServiceProviders via Laravel's service container:

**Example (`AccountingServiceProvider`):**
```php
public function register(): void
{
    $this->app->bind(AccountingServiceInterface::class, AccountingService::class);
}
```

**Binding Locations:**
- `ProductsServiceProvider`: Binds `ProductServiceInterface` → `ProductService`
- `InventoryServiceProvider`: Binds `InventoryServiceInterface` → `InventoryService`
- `AccountingServiceProvider`: Binds `AccountingServiceInterface` → `AccountingService`
- `SalesServiceProvider`: Binds `SaleRefundDataInterface` → `SaleRefundDataService`

### 4.4 Dependency Injection Pattern

Services inject dependencies via constructor:

**Example (`SaleService`):**
```php
class SaleService
{
    public function __construct(
        protected AccountingServiceInterface $accounting,
        protected InventoryServiceInterface $inventory,
        protected ProductServiceInterface $productService
    ) {}
}
```

**Benefits:**
- **Laravel Auto-Resolution**: Laravel automatically resolves dependencies
- **Testable**: Can inject mocks in tests
- **Type-Safe**: Type hints ensure correct dependencies
- **Loose Coupling**: Depends on interfaces, not concrete classes

### 4.5 Communication Flow Diagram

```
┌─────────────┐
│   Sales     │
└──────┬──────┘
       │
       ├───→ ProductServiceInterface (get product data)
       ├───→ InventoryServiceInterface (deduct stock)
       └───→ AccountingServiceInterface (record revenue)

┌─────────────┐
│  Expenses   │
└──────┬──────┘
       │
       └───→ AccountingServiceInterface (record expense)

┌─────────────┐
│  Refunds    │
└──────┬──────┘
       │
       ├───→ SaleRefundDataInterface (get sale data)
       ├───→ InventoryServiceInterface (add stock back)
       └───→ AccountingServiceInterface (reverse revenue)
```

### 4.6 Why Direct Model Access is Avoided

**Problem with Direct Model Access:**
```php
// BAD: Direct model access
$product = \Erp\Products\Models\Product::find($id);
$sale->product_id = $product->id;
```

**Issues:**
- **Tight Coupling**: Sales package depends on Products models
- **Breaking Changes**: Changes to Products models break Sales
- **Testing Difficulty**: Hard to mock models
- **Violates SOLID**: Dependency Inversion Principle violated

**Solution: Use Contracts**
```php
// GOOD: Contract-based access
$productData = $this->productService->getForSale($id);
if (!$productData) {
    throw new InvalidArgumentException('Product not found');
}
```

**Benefits:**
- **Loose Coupling**: Depends on interface, not implementation
- **Testable**: Can mock service interface
- **Flexible**: Can change implementation without breaking dependents
- **SOLID Compliant**: Follows Dependency Inversion Principle

---

## 5. Business Flows

### 5.1 Sale Creation Flow (`SaleService::createSale()`)

**Location:** `packages/Sales/src/Services/SaleService.php`

**Step-by-Step Execution:**

#### Step 1: Validation
```php
if (empty($items)) {
    throw new InvalidArgumentException('Sale must have at least one item.');
}
$currency = $currency ?? config('core.currency', 'USD');
```

#### Step 2: Transaction Start
```php
return DB::transaction(function () use (...) {
    // All operations inside transaction
});
```

#### Step 3: Create Sale Record
```php
$sale = Sale::create([
    'sale_number' => $this->generateSaleNumber(), // Format: S202402220001
    'warehouse_id' => $warehouseId,
    'branch_id' => $branchId,
    'currency' => $currency,
    'customer_name' => $customerName,
    'customer_email' => $customerEmail,
    'status' => 'completed',
    'sale_date' => now(),
]);
```

#### Step 4: Process Each Item
```php
foreach ($items as $item) {
    // 4a. Get product data (snapshot prices)
    $productData = $this->productService->getForSale((int) $item['product_id']);
    if (!$productData) {
        throw new InvalidArgumentException('Product not found or inactive');
    }
    
    // 4b. Validate quantity
    $quantity = (float) $item['quantity'];
    if ($quantity <= 0) {
        throw new InvalidArgumentException("Invalid quantity");
    }
    
    // 4c. Deduct inventory (validates stock availability)
    $this->inventory->deduct(
        $productData['id'],
        $warehouseId,
        $quantity,
        'sale',
        $sale->id
    );
    
    // 4d. Create SaleItem with snapshot prices
    SaleItem::create([
        'sale_id' => $sale->id,
        'product_id' => $productData['id'],
        'quantity' => $quantity,
        'cost_price' => $productData['cost_price'],      // Snapshot
        'selling_price' => $productData['selling_price'], // Snapshot
        'tax_percentage' => $productData['tax_percentage'],
    ]);
    
    // 4e. Accumulate total
    $totalAmount += $productData['selling_price'] * $quantity;
}
```

#### Step 5: Create Payments
```php
foreach ($payments as $payment) {
    Payment::create([
        'sale_id' => $sale->id,
        'amount' => $payment['amount'],
        'method' => $payment['method'],
        'reference' => $payment['reference'] ?? null,
        'paid_at' => now(),
    ]);
}
```

#### Step 6: Record Accounting Entry
```php
$revenueAccountId = $this->accounting->getAccountIdByCode('REVENUE');
$cashAccountId = $this->accounting->getAccountIdByCode('CASH');

if ($revenueAccountId && $cashAccountId && $totalAmount > 0) {
    $this->accounting->recordEntry(
        'Sale #' . $sale->sale_number,
        [
            ['account_id' => $cashAccountId, 'debit' => $totalAmount, 'credit' => 0],
            ['account_id' => $revenueAccountId, 'debit' => 0, 'credit' => $totalAmount],
        ],
        'sale',
        $sale->id,
        $currency,
        $branchId
    );
}
```

#### Step 7: Return Sale with Relations
```php
return $sale->load(['items', 'payments']);
```

**Key Points:**
- **Atomicity**: All operations in single transaction (all-or-nothing)
- **Price Snapshot**: Prices locked at sale time (prevents retroactive changes)
- **Stock Validation**: Happens inside `InventoryService::deduct()`
- **Accounting Integration**: Only creates entry if accounts exist and amount > 0
- **Reference Tracking**: Links inventory movements and accounting entries to sale

---

### 5.2 Refund Creation Flow (`RefundService::createRefund()`)

**Location:** `packages/Refunds/src/Services/RefundService.php`

**Step-by-Step Execution:**

#### Step 1: Get Sale Data
```php
$saleData = $this->saleRefundData->getSaleWithItemsForRefund($saleId);
if (!$saleData) {
    throw new InvalidArgumentException('Sale not found.');
}
$sale = $saleData['sale'];
$saleItemsIndex = collect($saleData['items'])->keyBy('id');
$currency = $currency ?? $sale['currency'];
```

#### Step 2: Validation
```php
if (empty($items)) {
    throw new InvalidArgumentException('Refund must have at least one item.');
}
// Validate each refund item belongs to sale
// Validate refund quantity <= original sale quantity
```

#### Step 3: Transaction Start
```php
return DB::transaction(function () use (...) {
    // All operations inside transaction
});
```

#### Step 4: Create Refund Record
```php
$refund = Refund::create([
    'refund_number' => $this->generateRefundNumber(), // Format: R202402220001
    'sale_id' => $sale['id'],
    'warehouse_id' => $warehouseId,
    'branch_id' => $branchId ?? $sale['branch_id'],
    'currency' => $currency,
    'status' => 'completed',
    'notes' => $notes,
    'refund_date' => now(),
]);
```

#### Step 5: Process Each Refund Item
```php
foreach ($items as $item) {
    $saleItemId = (int) $item['sale_item_id'];
    $saleItem = $saleItemsIndex->get($saleItemId);
    if (!$saleItem) {
        throw new InvalidArgumentException("Sale item does not belong to this sale");
    }
    
    $quantity = (float) $item['quantity'];
    if ($quantity <= 0 || $quantity > $saleItem['quantity']) {
        throw new InvalidArgumentException("Invalid refund quantity");
    }
    
    // 5a. Add inventory back (can be different warehouse)
    $this->inventory->add(
        $saleItem['product_id'],
        $warehouseId,
        $quantity,
        'refund',
        $refund->id
    );
    
    // 5b. Create RefundItem with original sale prices
    RefundItem::create([
        'refund_id' => $refund->id,
        'sale_item_id' => $saleItemId,
        'product_id' => $saleItem['product_id'],
        'quantity' => $quantity,
        'cost_price' => $saleItem['cost_price'],      // From original sale
        'selling_price' => $saleItem['selling_price'], // From original sale
    ]);
    
    // 5c. Accumulate total
    $totalAmount += $saleItem['selling_price'] * $quantity;
}
```

#### Step 6: Reverse Accounting Entry
```php
$revenueAccountId = $this->accounting->getAccountIdByCode('REVENUE');
$cashAccountId = $this->accounting->getAccountIdByCode('CASH');

if ($revenueAccountId && $cashAccountId && $totalAmount > 0) {
    $this->accounting->recordEntry(
        'Refund #' . $refund->refund_number . ' (reversal)',
        [
            ['account_id' => $revenueAccountId, 'debit' => $totalAmount, 'credit' => 0],  // Reverses credit
            ['account_id' => $cashAccountId, 'debit' => 0, 'credit' => $totalAmount],     // Reverses debit
        ],
        'refund',
        $refund->id,
        $currency,
        $refund->branch_id
    );
}
```

#### Step 7: Return Refund with Relations
```php
return $refund->load('items');
```

**Key Points:**
- **Never Deletes Sale**: Original sale preserved for audit trail
- **Original Prices**: Uses original sale prices (not current product prices)
- **Accounting Reversal**: Swaps debits/credits (Dr Revenue, Cr Cash)
- **Stock Return**: Can return stock to different warehouse than sale
- **Atomicity**: All operations in single transaction

---

### 5.3 Expense Creation Flow (`ExpenseService::createExpense()`)

**Location:** `packages/Expenses/src/Services/ExpenseService.php`

**Step-by-Step Execution:**

#### Step 1: Validation
```php
if ($amount <= 0) {
    throw new \InvalidArgumentException('Expense amount must be positive.');
}
$currency = $currency ?? config('core.currency', 'USD');
```

#### Step 2: Transaction Start
```php
return DB::transaction(function () use (...) {
    // All operations inside transaction
});
```

#### Step 3: Create Expense Record
```php
$expense = Expense::create([
    'expense_category_id' => $expenseCategoryId,
    'amount' => $amount,
    'currency' => $currency,
    'expense_date' => $expenseDate,
    'vendor_name' => $vendorName,
    'vendor_reference' => $vendorReference,
    'description' => $description,
    'branch_id' => $branchId,
]);
```

#### Step 4: Record Accounting Entry
```php
$expenseAccountId = $this->accounting->getAccountIdByCode('EXPENSE');
$cashAccountId = $this->accounting->getAccountIdByCode('CASH');

if ($expenseAccountId && $cashAccountId) {
    $this->accounting->recordEntry(
        'Expense: ' . ($description ?? 'Expense #' . $expense->id),
        [
            ['account_id' => $expenseAccountId, 'debit' => $amount, 'credit' => 0],
            ['account_id' => $cashAccountId, 'debit' => 0, 'credit' => $amount],
        ],
        'expense',
        $expense->id,
        $currency,
        $branchId
    );
}
```

#### Step 5: Return Expense
```php
return $expense;
```

**Key Points:**
- **Simple Flow**: Create expense + accounting entry
- **Standard Accounting**: Dr Expense, Cr Cash
- **Multi-Currency**: Supports different currencies
- **Multi-Branch**: Supports branch-specific expenses

---

## 6. Double-Entry Accounting

### 6.1 What is Double-Entry Accounting?

**Principle:** Every financial transaction affects at least two accounts, with total debits equaling total credits.

**Example:**
- **Sale**: Dr Cash $100, Cr Revenue $100
- **Expense**: Dr Expense $50, Cr Cash $50
- **Refund**: Dr Revenue $30, Cr Cash $30

### 6.2 Database Schema

#### `accounts` Table
Stores chart of accounts:
- `id`: Primary key
- `name`: Account name (e.g., "Cash", "Revenue", "Expenses")
- `code`: Unique code (e.g., "CASH", "REVENUE", "EXPENSE")
- `type`: Account type (asset, liability, equity, revenue, expense)
- `branch_id`: Optional branch filter
- `is_active`: Active flag

#### `journal_entries` Table
Stores accounting entries:
- `id`: Primary key
- `description`: Entry description
- `reference_type`: Polymorphic reference type (e.g., "sale", "expense", "refund")
- `reference_id`: Polymorphic reference ID
- `currency`: Currency code (default 'USD')
- `branch_id`: Optional branch filter
- `entry_date`: Accounting date
- `created_at`, `updated_at`, `deleted_at`: Timestamps (soft deletes)

#### `journal_lines` Table
Stores debit/credit lines:
- `id`: Primary key
- `journal_entry_id`: Foreign key to `journal_entries`
- `account_id`: Foreign key to `accounts`
- `debit`: Debit amount (0 if credit)
- `credit`: Credit amount (0 if debit)
- **No timestamps** (immutable accounting records)
- **No soft deletes** (accounting entries are permanent)

### 6.3 How Transactions are Balanced

**Validation in `AccountingService::recordEntry()`:**
```php
$totalDebit = 0.0;
$totalCredit = 0.0;

foreach ($lines as $line) {
    $debit = (float) ($line['debit'] ?? 0);
    $credit = (float) ($line['credit'] ?? 0);
    $totalDebit += $debit;
    $totalCredit += $credit;
}

if (abs($totalDebit - $totalCredit) > 0.0001) {
    throw new InvalidArgumentException(
        'Journal entry must balance. Debits: ' . $totalDebit . ', Credits: ' . $totalCredit
    );
}
```

**Why 0.0001 Tolerance?**
- Floating-point precision issues
- Allows small rounding differences
- Prevents false positives

### 6.4 Account Balance Calculation

**Method:** `AccountingService::getAccountBalance()`

```php
public function getAccountBalance(int $accountId, ?int $branchId = null): float
{
    $query = JournalLine::query()
        ->where('account_id', $accountId)
        ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id');
    
    if ($branchId !== null) {
        $query->where('journal_entries.branch_id', $branchId);
    }
    
    $totals = $query->selectRaw('SUM(journal_lines.debit) as total_debit, SUM(journal_lines.credit) as total_credit')
        ->first();
    
    $debit = (float) ($totals->total_debit ?? 0);
    $credit = (float) ($totals->total_credit ?? 0);
    
    return $debit - $credit;
}
```

**Formula:** `Balance = SUM(Debits) - SUM(Credits)`

**Examples:**
- **Asset Account (Cash)**: Positive balance = cash on hand
- **Revenue Account**: Negative balance = total revenue (credits > debits)
- **Expense Account**: Positive balance = total expenses (debits > credits)

### 6.5 Example: Sale Accounting Entry

**Sale Transaction:**
- Customer pays $100 cash for products

**Accounting Entry:**
```php
[
    ['account_id' => $cashAccountId, 'debit' => 100, 'credit' => 0],
    ['account_id' => $revenueAccountId, 'debit' => 0, 'credit' => 100],
]
```

**Result:**
- Cash account balance increases by $100 (debit)
- Revenue account balance decreases by $100 (credit = negative for revenue)

### 6.6 Example: Expense Accounting Entry

**Expense Transaction:**
- Company pays $50 cash for office supplies

**Accounting Entry:**
```php
[
    ['account_id' => $expenseAccountId, 'debit' => 50, 'credit' => 0],
    ['account_id' => $cashAccountId, 'debit' => 0, 'credit' => 50],
]
```

**Result:**
- Expense account balance increases by $50 (debit)
- Cash account balance decreases by $50 (credit)

### 6.7 Why Journal Lines are Immutable

**Design Decision:** `JournalLine` model does NOT use `BaseModel` (no timestamps, no soft deletes)

**Reasons:**
- **Audit Compliance**: Accounting records must be permanent
- **Financial Integrity**: Prevents accidental or malicious changes
- **Regulatory Requirements**: Many jurisdictions require immutable accounting records
- **Error Correction**: Errors corrected via reversal entries, not edits

**How Errors are Corrected:**
- Use `AccountingService::reverseEntry()` to create reversal entry
- Creates new entry with swapped debits/credits
- Maintains audit trail

---

## 7. Refund Processing

### 7.1 Refund Philosophy

**Principle:** Refunds never delete original sales. They create new records that reverse the effects of the sale.

**Why?**
- **Audit Trail**: Maintains complete financial history
- **Compliance**: Required for financial reporting
- **Transparency**: Shows both sale and refund in records
- **Analysis**: Enables refund rate analysis

### 7.2 Technical Implementation

#### Step 1: Get Original Sale Data
```php
$saleData = $this->saleRefundData->getSaleWithItemsForRefund($saleId);
```
- Retrieves sale with all line items
- Uses original sale prices (not current product prices)
- Validates sale exists

#### Step 2: Return Stock
```php
$this->inventory->add(
    $saleItem['product_id'],
    $warehouseId,  // Can be different from sale warehouse
    $quantity,
    'refund',
    $refund->id
);
```
- Adds stock back to inventory
- Can return to different warehouse than sale
- Creates IN movement with reference to refund

#### Step 3: Reverse Accounting Entry
```php
$this->accounting->recordEntry(
    'Refund #' . $refund->refund_number . ' (reversal)',
    [
        ['account_id' => $revenueAccountId, 'debit' => $totalAmount, 'credit' => 0],  // Reverses credit
        ['account_id' => $cashAccountId, 'debit' => 0, 'credit' => $totalAmount],     // Reverses debit
    ],
    'refund',
    $refund->id,
    $currency,
    $refund->branch_id
);
```

**Original Sale Entry:**
- Dr Cash $100, Cr Revenue $100

**Refund Entry (Reversal):**
- Dr Revenue $100, Cr Cash $100

**Net Effect:**
- Cash: $100 - $100 = $0 (no change)
- Revenue: -$100 + $100 = $0 (no change)

### 7.3 Preserving Financial History

**Database Records After Refund:**
- **Sale Record**: Still exists (never deleted)
- **Sale Items**: Still exist (never deleted)
- **Refund Record**: New record linking to sale
- **Refund Items**: New records using original sale prices
- **Accounting Entries**: Both sale and refund entries exist

**Benefits:**
- **Complete Audit Trail**: Can see both sale and refund
- **Financial Reporting**: Reports show net sales (sales - refunds)
- **Analysis**: Can analyze refund rates, reasons, etc.
- **Compliance**: Meets regulatory requirements

### 7.4 Partial Refunds

**Support:** System supports partial refunds (refund some items, not all)

**Implementation:**
- Refund items reference specific `sale_item_id`
- Quantity can be less than original sale quantity
- Validation ensures refund quantity <= original sale quantity

**Example:**
- **Original Sale**: 10 units @ $10 each = $100
- **Partial Refund**: 3 units @ $10 each = $30
- **Remaining Sale**: 7 units @ $10 each = $70

---

## 8. Profit Calculation

### 8.1 Gross Profit

**Definition:** Revenue minus cost of goods sold (COGS)

**Formula:** `Gross Profit = SUM((selling_price - cost_price) * quantity)`

**Implementation:** `ProfitService::calculateGrossProfit()`

```php
public function calculateGrossProfit(array $lineItems): float
{
    $total = 0.0;
    
    foreach ($lineItems as $item) {
        $sellingPrice = (float) ($item['selling_price'] ?? 0);
        $costPrice = (float) ($item['cost_price'] ?? 0);
        $quantity = (float) ($item['quantity'] ?? 0);
        $total += ($sellingPrice - $costPrice) * $quantity;
    }
    
    return $total;
}
```

**Example:**
- Product A: 10 units @ $10 selling, $6 cost = ($10 - $6) * 10 = $40
- Product B: 5 units @ $20 selling, $12 cost = ($20 - $12) * 5 = $40
- **Gross Profit**: $40 + $40 = $80

### 8.2 Net Profit

**Definition:** Gross profit minus total expenses

**Formula:** `Net Profit = Gross Profit - Total Expenses`

**Implementation:** `ProfitService::calculateNetProfit()`

```php
public function calculateNetProfit(float $grossProfit, float $totalExpenses): float
{
    return $grossProfit - $totalExpenses;
}
```

**Example:**
- Gross Profit: $80
- Total Expenses: $30
- **Net Profit**: $80 - $30 = $50

### 8.3 Why Profit is Not Stored Directly

**Design Decision:** Profit is calculated on-demand, not stored in database

**Reasons:**
- **Accuracy**: Profit depends on current data (sales, expenses)
- **Real-Time**: Always reflects current state
- **Flexibility**: Can calculate for any time period
- **No Synchronization**: Avoids keeping calculated values in sync

**How to Calculate Profit:**

**From Sales:**
```php
$sale = Sale::with('items')->find($saleId);
$lineItems = $sale->items->map(function ($item) {
    return [
        'selling_price' => $item->selling_price,
        'cost_price' => $item->cost_price,
        'quantity' => $item->quantity,
    ];
})->toArray();

$grossProfit = app(ProfitService::class)->calculateGrossProfit($lineItems);
```

**From Expenses:**
```php
$totalExpenses = Expense::where('expense_date', '>=', $startDate)
    ->where('expense_date', '<=', $endDate)
    ->sum('amount');
```

**Net Profit:**
```php
$netProfit = app(ProfitService::class)->calculateNetProfit($grossProfit, $totalExpenses);
```

### 8.4 ProfitService Design

**Pure Calculation Service:**
- **No Database Access**: Does not query models directly
- **Stateless**: No internal state
- **Reusable**: Can calculate profit from any data source
- **Testable**: Easy to unit test with mock data

**Why Pure?**
- **Separation of Concerns**: Calculation logic separate from data access
- **Testability**: Can test calculation logic without database
- **Flexibility**: Can calculate profit from API data, CSV, etc.

---

## 9. Design Patterns

### 9.1 Service Layer Pattern

**Where Used:** All packages expose business logic through service classes

**Why:**
- **Separation of Concerns**: Business logic separate from controllers
- **Reusability**: Services can be used by controllers, jobs, commands
- **Testability**: Services can be tested independently
- **Transaction Management**: Services handle transaction boundaries

**Examples:**
- `SaleService::createSale()` - Orchestrates sale creation
- `RefundService::createRefund()` - Orchestrates refund processing
- `ExpenseService::createExpense()` - Orchestrates expense creation
- `AccountingService::recordEntry()` - Handles accounting logic
- `InventoryService::deduct()` / `add()` - Handles stock operations

**Pattern Structure:**
```php
class SaleService
{
    public function __construct(
        protected AccountingServiceInterface $accounting,
        protected InventoryServiceInterface $inventory,
        protected ProductServiceInterface $productService
    ) {}
    
    public function createSale(...): Sale
    {
        return DB::transaction(function () {
            // Business logic here
        });
    }
}
```

### 9.2 Contract/Interface Pattern

**Where Used:** Inter-package communication

**Why:**
- **Decoupling**: Packages depend on abstractions, not implementations
- **Testability**: Can inject mocks for testing
- **Flexibility**: Can swap implementations without changing dependents
- **SOLID Compliance**: Follows Dependency Inversion Principle

**Implementation:**
```php
// Contract (interface)
interface AccountingServiceInterface
{
    public function recordEntry(...): object;
}

// Implementation
class AccountingService implements AccountingServiceInterface
{
    // Implementation
}

// Usage (dependency injection)
class SaleService
{
    public function __construct(
        protected AccountingServiceInterface $accounting  // Depends on interface
    ) {}
}
```

### 9.3 Dependency Injection

**Where Used:** Constructor injection in all service classes

**Benefits:**
- **Laravel Auto-Resolution**: Laravel automatically resolves dependencies
- **Testable**: Can inject mocks in tests
- **Type-Safe**: Type hints ensure correct dependencies
- **Loose Coupling**: Depends on interfaces, not concrete classes

**Example:**
```php
class SaleService
{
    public function __construct(
        protected AccountingServiceInterface $accounting,
        protected InventoryServiceInterface $inventory,
        protected ProductServiceInterface $productService
    ) {}
}
```

**Laravel Resolution:**
1. Checks service container bindings
2. Resolves `AccountingServiceInterface` → `AccountingService`
3. Injects resolved instance into constructor

### 9.4 Transaction Handling

**Where Used:** All critical business operations

**Pattern:** `DB::transaction(function () { ... })`

**Why:**
- **Atomicity**: Ensures all-or-nothing execution
- **Data Integrity**: Prevents partial updates
- **Consistency**: Maintains database consistency
- **Error Recovery**: Automatic rollback on exception

**Examples:**
- `SaleService::createSale()` - Wraps sale creation, inventory deduction, accounting entry
- `RefundService::createRefund()` - Wraps refund creation, inventory addition, accounting reversal
- `ExpenseService::createExpense()` - Wraps expense creation, accounting entry
- `AccountingService::recordEntry()` - Wraps journal entry + lines creation
- `InventoryService::deduct()` / `add()` - Wraps stock movement creation

**Nested Transactions:**
- Laravel supports nested transactions
- Inner transaction commits with outer transaction
- If inner fails, outer also rolls back

### 9.5 Event Sourcing (Stock Movements)

**Pattern:** Stock tracking via events (movements) rather than balance table

**Implementation:**
- `stock_movements` table stores all IN/OUT events
- Stock calculated: `SUM(IN) - SUM(OUT)` per product/warehouse
- No `stock_balances` table

**Benefits:**
- **Complete Audit Trail**: Every stock change is recorded
- **Historical Reconstruction**: Can calculate stock at any point in time
- **No Synchronization Issues**: No balance table to keep in sync
- **Reference Tracking**: Links movements to sales, refunds, etc.

**Trade-offs:**
- **Performance**: Requires calculation for current stock (acceptable for ERP scale)
- **Indexes**: Indexes on `[product_id, warehouse_id]` optimize queries

**Calculation:**
```php
public function getStock(int $productId, int $warehouseId): float
{
    $in = StockMovement::where('product_id', $productId)
        ->where('warehouse_id', $warehouseId)
        ->where('type', StockMovement::TYPE_IN)
        ->sum('quantity');
    
    $out = StockMovement::where('product_id', $productId)
        ->where('warehouse_id', $warehouseId)
        ->where('type', StockMovement::TYPE_OUT)
        ->sum('quantity');
    
    return $in - $out;
}
```

### 9.6 Snapshot Pattern (Price Locking)

**Where Used:** Sales and Refunds

**Implementation:**
- `sale_items` stores `cost_price`, `selling_price`, `tax_percentage` at sale time
- `refund_items` stores prices from original sale (not current product prices)

**Why:**
- **Historical Accuracy**: Prices don't change retroactively
- **Financial Reporting Accuracy**: Reports reflect actual sale prices
- **Audit Compliance**: Required for financial audits
- **Profit Calculation**: Accurate profit calculation requires historical prices

**Example:**
- **Sale Date**: Jan 1, 2024
- **Product Price at Sale**: $10
- **Product Price Today**: $15
- **Sale Record**: Still shows $10 (snapshot)

### 9.7 Repository Pattern

**Status:** Partially implemented

**Evidence:**
- `ProductRepositoryInterface` exists but not actively used
- `ProductService` directly uses Eloquent models
- No repository implementations found

**Recommendation:** Could be expanded for better testability and abstraction

**Potential Implementation:**
```php
interface ProductRepositoryInterface
{
    public function find(int $id): ?Product;
    public function findActive(int $id): ?Product;
}

class ProductRepository implements ProductRepositoryInterface
{
    public function find(int $id): ?Product
    {
        return Product::find($id);
    }
}
```

### 9.8 SOLID Principles

**Single Responsibility Principle (SRP):**
- Each service has one responsibility
- `SaleService` handles sales, `RefundService` handles refunds
- `AccountingService` handles accounting, `InventoryService` handles inventory

**Open/Closed Principle (OCP):**
- Packages open for extension (via contracts)
- Closed for modification (implementations can be swapped)

**Liskov Substitution Principle (LSP):**
- Interface implementations are substitutable
- Any `AccountingServiceInterface` implementation works

**Interface Segregation Principle (ISP):**
- Contracts are focused (not bloated)
- `ProductServiceInterface` has one method: `getForSale()`

**Dependency Inversion Principle (DIP):**
- High-level modules depend on abstractions (interfaces)
- Low-level modules implement interfaces
- `SaleService` depends on `AccountingServiceInterface`, not `AccountingService`

---

## 10. Multi-Tenancy Support

### 10.1 Branch Support

**Implementation:**
- `branch_id` column in multiple tables (nullable)
- No `branches` table (assumes external management or future addition)
- Accounting balances filter by `branch_id` when provided

**Tables with `branch_id`:**
- `warehouses.branch_id`
- `accounts.branch_id`
- `journal_entries.branch_id`
- `sales.branch_id`
- `expenses.branch_id`
- `refunds.branch_id`

**Usage:**
- All `branch_id` columns nullable (supports single-branch)
- `AccountingService::getAccountBalance()` filters by branch if provided
- No global branch filtering middleware (would need to be added)

**Example:**
```php
// Get account balance for specific branch
$balance = $accounting->getAccountBalance($accountId, $branchId);

// Get account balance for all branches
$balance = $accounting->getAccountBalance($accountId, null);
```

**Future Enhancement:**
- Add `branches` table
- Add global branch filtering middleware
- Add branch-based access control

### 10.2 Warehouse Support

**Implementation:**
- `warehouses` table with `branch_id` (nullable)
- Stock tracked per `[product_id, warehouse_id]`
- Sales and refunds reference warehouse

**Features:**
- Multi-warehouse stock tracking
- Warehouse can belong to branch (via `branch_id`)
- Stock movements reference warehouse
- Stock queries filter by warehouse

**Example:**
```php
// Get stock for product in specific warehouse
$stock = $inventory->getStock($productId, $warehouseId);

// Deduct stock from warehouse
$inventory->deduct($productId, $warehouseId, $quantity, 'sale', $saleId);
```

**Use Cases:**
- **Multi-Location**: Different warehouses in different cities
- **Branch Warehouses**: Each branch has its own warehouse
- **Stock Transfers**: Can transfer stock between warehouses (future feature)

### 10.3 Currency Support

**Implementation:**
- `currency` column (3 chars, default 'USD') in:
  - `journal_entries`
  - `sales`
  - `expenses`
  - `refunds`
- Default currency from `config('core.currency')` or `ERP_CURRENCY` env

**Limitations:**
- No currency conversion logic
- Assumes single currency per transaction
- No exchange rate tracking

**Example:**
```php
// Create sale in USD
$sale = $saleService->createSale(..., currency: 'USD');

// Create sale in EUR
$sale = $saleService->createSale(..., currency: 'EUR');
```

**Future Enhancement:**
- Add currency conversion service
- Add exchange rate tracking
- Support multi-currency transactions

### 10.4 Future Microservice Extraction

**Current Architecture Supports:**
- **Contract-Based Communication**: Interfaces enable service extraction
- **Independent Packages**: Each package can become a microservice
- **Service Layer**: Business logic already separated from controllers
- **Database Per Package**: Each package has its own migrations (can become separate DB)

**Extraction Strategy:**

**Step 1: Extract Accounting Service**
- Move `Accounting` package to separate service
- Expose `AccountingServiceInterface` via API
- Update dependents to call API instead of local service

**Step 2: Extract Inventory Service**
- Move `Inventory` package to separate service
- Expose `InventoryServiceInterface` via API
- Update Sales to call API

**Step 3: Extract Products Service**
- Move `Products` package to separate service
- Expose `ProductServiceInterface` via API
- Update Sales to call API

**Step 4: Extract Sales Service**
- Move `Sales` package to separate service
- Expose sales API
- Update main app to call API

**Benefits:**
- **Scalability**: Each service can scale independently
- **Technology Diversity**: Each service can use different tech stack
- **Team Ownership**: Each team owns a service
- **Deployment Independence**: Each service can be deployed independently

**Challenges:**
- **Distributed Transactions**: Need saga pattern or eventual consistency
- **Network Latency**: API calls slower than local calls
- **Service Discovery**: Need service registry
- **Monitoring**: Need distributed tracing

---

## Summary

This Laravel ERP system is a **modular monolith** with clear package boundaries, contract-based communication, and transaction-safe operations. It supports multi-branch, multi-warehouse, and multi-currency scenarios, uses event-sourced stock tracking, and maintains audit trails through soft deletes and reference tracking.

### Key Strengths

- **Clear Separation of Concerns**: Each package has a single responsibility
- **Contract-Based Communication**: Packages communicate via interfaces
- **Transaction Safety**: All critical operations are atomic
- **Price Snapshot**: Historical accuracy for financial reporting
- **Event-Sourced Inventory**: Complete audit trail for stock movements
- **Multi-Tenant Ready**: Supports branches, warehouses, currencies
- **SOLID Principles**: Follows best practices for maintainability

### Areas for Enhancement

- **Repository Pattern**: Not fully utilized (could improve testability)
- **Currency Conversion**: No exchange rate tracking
- **Branch Filtering**: No global middleware for branch filtering
- **Profit Integration**: `ProfitService` not integrated into main flows
- **Audit Logging**: Infrastructure exists but not actively used
- **Microservice Extraction**: Architecture supports it, but not yet implemented

### Architecture Suitability

This architecture is suitable for:
- **Production ERP Systems**: Requires modularity, testability, and data integrity
- **Multi-Tenant Applications**: Supports branches, warehouses, currencies
- **Financial Systems**: Double-entry accounting with immutable records
- **Future Growth**: Can evolve into microservices if needed

---

**Document Version:** 1.0  
**Last Updated:** February 2026  
**Author:** Laravel Architecture Analysis

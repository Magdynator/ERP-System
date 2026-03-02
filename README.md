# 🏢 Modular ERP System

A **production-grade, modular monolith ERP system** built with Laravel 12. Designed with clean architecture principles — each business domain lives in its own independent package, communicating through well-defined contracts (interfaces).

![PHP](https://img.shields.io/badge/PHP-8.2+-8892BF?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-4.0-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)

---

## ✨ Features

- **Modular Architecture** — 7 independent packages with contract-based communication
- **Double-Entry Accounting** — Fully compliant accounting system with immutable journal entries
- **Inventory Management** — Event-sourced stock tracking with full audit trail
- **Sales & Invoicing** — Complete sales flow with price snapshots, payments, and PDF invoice generation
- **Expense Tracking** — Categorized expenses with automatic accounting entries
- **Refund Processing** — Full reversal of sales, stock returns, and accounting corrections
- **User Management** — Role-based access control with Spatie Permissions
- **Audit Logging** — Track all model changes with user attribution
- **REST API** — Fully documented with Swagger/OpenAPI (L5-Swagger)
- **Web Dashboard** — Blade-based UI with Tailwind CSS 4
- **Multi-Currency Support** — Configurable default currency
- **Branch Support** — Optional multi-branch/multi-tenant filtering

---

## 🏗️ Architecture

This system follows a **Modular Monolith** pattern. All business domains are organized as independent Composer packages under the `packages/` directory.

### Dependency Graph

```
Core (foundation — no dependencies)
├── Products (depends on Core)
├── Accounting (depends on Core)
├── Inventory (depends on Core, Products)
├── Expenses (depends on Core, Accounting)
├── Sales (depends on Core, Products, Inventory, Accounting)
└── Refunds (depends on Core, Products, Inventory, Accounting, Sales)
```

### Key Design Decisions

| Principle | Implementation |
|---|---|
| **Contract-Based Communication** | Packages interact via interfaces, never direct model access |
| **Dependency Injection** | Laravel's service container auto-resolves all dependencies |
| **Event-Sourced Inventory** | Stock calculated from movement history — no balance table to desync |
| **Price Snapshots** | Sale items store prices at sale-time, preventing retroactive changes |
| **Immutable Accounting** | Journal lines have no timestamps or soft deletes — permanent records |
| **Independent Migrations** | Each package manages its own database schema |

> 📖 For a deep dive, see [ARCHITECTURE.md](ARCHITECTURE.md)

---

## 📦 Modules

### Core
Foundation package providing `BaseModel` (soft deletes + timestamps), user management, audit logging, and shared services like profit calculation.

### Products
Product catalog with hierarchical categories. Provides the `ProductServiceInterface` to snapshot product prices for sales.

### Inventory
Warehouse and stock movement management. Uses event-sourced stock tracking (IN/OUT movements) for a complete audit trail.

### Accounting
Double-entry accounting system with chart of accounts, journal entries, and balanced debit/credit lines. Used by Sales, Expenses, and Refunds.

### Sales
Sales order management orchestrating product lookup → inventory deduction → sale creation → accounting entry — all within a single database transaction.

### Expenses
Expense tracking with categorization and automatic accounting journal entries (Dr Expense, Cr Cash).

### Refunds
Refund processing that reverses sales operations: returns stock to inventory, creates reversal accounting entries, and maintains full traceability to the original sale.

---

## 🚀 Getting Started

### Prerequisites

- **PHP** ≥ 8.2
- **Composer** ≥ 2.x
- **Node.js** ≥ 18.x & **npm**
- **SQLite** (default) or **MySQL** 8.x / **PostgreSQL** 15+

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/your-username/erp-system.git
cd erp-system

# 2. Run the automated setup
composer setup
```

The `composer setup` command will:
- Install PHP dependencies
- Copy `.env.example` → `.env`
- Generate application key
- Run all migrations
- Install Node.js dependencies
- Build frontend assets

### Manual Setup (Alternative)

```bash
# Install PHP dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Install and build frontend
npm install
npm run build
```

### Development Server

```bash
# Start all services concurrently (server, queue, logs, vite)
composer dev
```

This starts:
| Service | Description |
|---|---|
| `php artisan serve` | Laravel development server |
| `php artisan queue:listen` | Queue worker |
| `php artisan pail` | Real-time log viewer |
| `npm run dev` | Vite dev server with HMR |

The app will be available at **http://localhost:8000**

---

## 🔌 API Documentation

The API is fully documented with **Swagger/OpenAPI** via [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger).

```bash
# Generate API docs
php artisan l5-swagger:generate
```

Then visit: **http://localhost:8000/api/documentation**

### API Endpoints Overview

| Module | Base Path | Key Endpoints |
|---|---|---|
| Products | `/api/v1/products` | CRUD operations for products |
| Categories | `/api/v1/categories` | CRUD operations for categories |
| Warehouses | `/api/v1/warehouses` | Warehouse management |
| Stock | `/api/v1/stock` | Stock queries and movements |
| Accounts | `/api/v1/accounts` | Chart of accounts management |
| Journal Entries | `/api/v1/journal-entries` | Accounting entries |
| Sales | `/api/v1/sales` | Sales order management |
| Expenses | `/api/v1/expenses` | Expense tracking |
| Refunds | `/api/v1/refunds` | Refund processing |

All API routes are protected with **Laravel Sanctum** token-based authentication.

---

## 🖥️ Web Dashboard

The system includes a full **Blade-based web interface** with:

- **Dashboard** — Overview and key metrics
- **Product Management** — Categories and products CRUD
- **Warehouse & Stock** — Warehouse management and stock levels
- **Sales** — Create sales, view history, generate PDF invoices
- **Accounting** — Chart of accounts and journal entries (permission-gated)
- **Expenses** — Track and categorize expenses (permission-gated)
- **Refunds** — Process returns against existing sales
- **Audit Logs** — Review all system changes (permission-gated)
- **User Management** — Manage users and roles (permission-gated)

---

## 🧪 Testing

```bash
# Run the test suite
composer test

# Or directly
php artisan test
```

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | PHP 8.2+, Laravel 12 |
| **Frontend** | Blade Templates, Tailwind CSS 4, Vite 7 |
| **Authentication** | Laravel Sanctum (API), Session (Web) |
| **Authorization** | Spatie Laravel Permission |
| **Database** | SQLite (default) / MySQL / PostgreSQL |
| **PDF Generation** | Laravel DomPDF |
| **API Docs** | L5-Swagger (OpenAPI 3) |
| **Testing** | PHPUnit 11 |
| **Code Style** | Laravel Pint |

---

## 📁 Project Structure

```
mainapp/
├── app/                          # Application code (controllers, middleware)
│   ├── Http/Controllers/
│   │   ├── Auth/                 # Authentication controllers
│   │   ├── Web/                  # Blade UI controllers
│   │   └── DashboardController
│   └── Providers/
├── packages/                     # ERP modules (independent packages)
│   ├── Core/                     # Foundation (BaseModel, Users, Audit)
│   ├── Products/                 # Product catalog & categories
│   ├── Inventory/                # Warehouses & stock movements
│   ├── Sales/                    # Sales orders & payments
│   ├── Accounting/               # Double-entry accounting
│   ├── Expenses/                 # Expense tracking
│   └── Refunds/                  # Refund processing
├── resources/views/              # Blade templates
├── routes/
│   ├── web.php                   # Web routes
│   └── api.php                   # API route aggregation
├── database/
│   ├── migrations/               # App-level migrations
│   └── seeders/                  # Database seeders
└── docs/                         # Additional documentation
```

Each package follows a consistent internal structure:

```
PackageName/
├── src/
│   ├── Models/                   # Eloquent models
│   ├── Services/                 # Business logic
│   ├── Contracts/                # Interfaces for inter-package communication
│   ├── Http/Controllers/         # API controllers
│   └── Providers/                # Service provider (routes, config, bindings)
├── Database/Migrations/          # Package-specific migrations
├── Routes/api.php                # Package API routes
└── Config/                       # Package configuration
```

---

## 📄 License

This project is open-sourced software licensed under the [MIT License](LICENSE).

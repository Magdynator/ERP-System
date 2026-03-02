# Cuerp API Documentation

Welcome to the Cuerp API documentation. This document provides a comprehensive overview of all available RESTful API endpoints for managing the core ERP modules.

---

## Table of Contents
1. [General Information](#general-information)
2. [Authentication](#authentication)
3. [Products](#products)
4. [Categories](#categories)
5. [Sales](#sales)
6. [Refunds](#refunds)
7. [Expenses](#expenses)
8. [Inventory & Warehouses](#inventory--warehouses)
9. [Accounting](#accounting)

---

## General Information

- **Base URL**: `/api/v1`
- **Content Type**: `application/json`
- **Accept**: `application/json`

### Standard Responses
**Success (200 OK / 201 Created)**
```json
{
    "data": { ... },
    "message": "Resource successfully retrieved/created"
}
```

**Validation Error (422 Unprocessable Entity)**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "field_name": [
            "The field_name field is required."
        ]
    }
}
```

**Not Found (404 Not Found)**
```json
{
    "message": "Resource not found."
}
```

**Unauthorized (401 Unauthorized)**
```json
{
    "message": "Unauthenticated."
}
```

---

## Authentication

The API uses Laravel Sanctum for authentication. If you are interacting via a SPA (Single Page Application), authentication is handled automatically through session cookies after login. If you are interacting via external clients, issue a standard authentication token.

### 1. Login
- **Endpoint**: `/login` *(Base web route)*
- **Method**: `POST`
- **Headers**: `Accept: application/json`

**Request Body**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `email` | `string` | Yes | Registered email address |
| `password` | `string` | Yes | User password |

### 2. Register
- **Endpoint**: `/register` *(Base web route)*
- **Method**: `POST`
- **Headers**: `Accept: application/json`

### 3. Logout
- **Endpoint**: `/logout` *(Base web route)*
- **Method**: `POST`
- **Headers**: `Accept: application/json`

---

## Products

Manage the catalog of products for sales and inventory.

### 1. List Products
- **Endpoint**: `/api/v1/products`
- **Method**: `GET`
- **Description**: Returns a paginated list of all products.

### 2. Create Product
- **Endpoint**: `/api/v1/products`
- **Method**: `POST`
- **Description**: Creates a new product and initializes stock.

**Request Body**
```json
{
  "name": "Standard Widget",
  "sku": "WIDG-001",
  "category_id": 1,
  "cost_price": 10.50,
  "selling_price": 25.00,
  "tax_percentage": 10.0,
  "is_active": 1,
  "warehouse_id": 1,
  "initial_quantity": 50
}
```

### 3. Get Product Details
- **Endpoint**: `/api/v1/products/{product_id}`
- **Method**: `GET`
- **Description**: Retrieves detailed information for a specific product.

### 4. Update Product
- **Endpoint**: `/api/v1/products/{product_id}`
- **Method**: `PUT`
- **Description**: Updates an existing product.

### 5. Delete Product
- **Endpoint**: `/api/v1/products/{product_id}`
- **Method**: `DELETE`

---

## Categories

Group your products functionally.

### 1. List Categories
- **Endpoint**: `/api/v1/categories`
- **Method**: `GET`

### 2. Create Category
- **Endpoint**: `/api/v1/categories`
- **Method**: `POST`

**Request Body**
```json
{
  "name": "Electronics",
  "slug": "electronics",
  "parent_id": null,
  "description": "Consumer electronics.",
  "is_active": 1
}
```

### 3. Get, Update, Delete Category
- **Get**: `GET /api/v1/categories/{category_id}`
- **Update**: `PUT /api/v1/categories/{category_id}`
- **Delete**: `DELETE /api/v1/categories/{category_id}`

---

## Sales

Manage outbound sales and invoices. 

### 1. List Sales
- **Endpoint**: `/api/v1/sales`
- **Method**: `GET`

### 2. Create Sale
- **Endpoint**: `/api/v1/sales`
- **Method**: `POST`

**Request Body**
```json
{
  "warehouse_id": 1,
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "notes": "Handle with care",
  "currency": "USD",
  "items": [
    {
      "product_id": 5,
      "quantity": 2,
      "selling_price": 25.00
    }
  ]
}
```

### 3. Get, Update, Delete Sale
- **Get**: `GET /api/v1/sales/{sale_id}`
- **Update**: `PUT /api/v1/sales/{sale_id}`
- **Delete**: `DELETE /api/v1/sales/{sale_id}`

---

## Refunds

Manage return processing from previous sales.

### 1. List Refunds
- **Endpoint**: `/api/v1/refunds`
- **Method**: `GET`

### 2. Process Refund
- **Endpoint**: `/api/v1/refunds`
- **Method**: `POST`
- **Description**: Returns stock to a warehouse based on a specific sale item.

**Request Body**
```json
{
  "sale_id": 105,
  "warehouse_id": 1,
  "currency": "USD",
  "notes": "Defective item returned",
  "items": [
    {
      "sale_item_id": 12,
      "quantity": 1
    }
  ]
}
```

### 3. Get Refund Details
- **Endpoint**: `GET /api/v1/refunds/{refund_id}`

---

## Expenses

Manage outgoing cash flow not strictly tied to COGS (Cost of Goods Sold).

### 1. List Expenses
- **Endpoint**: `/api/v1/expenses`
- **Method**: `GET`

### 2. Create Expense
- **Endpoint**: `/api/v1/expenses`
- **Method**: `POST`

**Request Body**
```json
{
  "amount": 150.00,
  "expense_date": "2026-02-23",
  "account_id": 2,
  "currency": "USD",
  "notes": "Office supplies"
}
```

### 3. Get, Update, Delete Expense
- **Get**: `GET /api/v1/expenses/{expense_id}`
- **Update**: `PUT /api/v1/expenses/{expense_id}`
- **Delete**: `DELETE /api/v1/expenses/{expense_id}`

---

## Inventory & Warehouses

### 1. List Warehouses
- **Endpoint**: `/api/v1/warehouses`
- **Method**: `GET`

### 2. Create Warehouse
- **Endpoint**: `/api/v1/warehouses`
- **Method**: `POST`

**Request Body**
```json
{
  "name": "Main Distribution Center",
  "code": "MAIN-01",
  "is_active": 1
}
```

### 3. Get, Update, Delete Warehouse
- **Get**: `GET /api/v1/warehouses/{warehouse_id}`
- **Update**: `PUT /api/v1/warehouses/{warehouse_id}`
- **Delete**: `DELETE /api/v1/warehouses/{warehouse_id}`

### 4. Fetch Stock
- **Endpoint**: `/api/v1/stock`
- **Method**: `GET`
- **Description**: Returns available inventory balances.

### 5. Fetch Stock Movements
- **Endpoint**: `/api/v1/stock-movements`
- **Method**: `GET`

---

## Accounting

Manage double-entry bookkeeping ledgers.

### 1. List Accounts
- **Endpoint**: `/api/v1/accounts`
- **Method**: `GET`

### 2. Create Account
- **Endpoint**: `/api/v1/accounts`
- **Method**: `POST`

**Request Body**
```json
{
  "name": "Cash equivalent",
  "code": "1001",
  "type": "asset",
  "is_active": 1
}
```

*Valid enum types: `asset`, `liability`, `equity`, `revenue`, `expense`*

### 3. Get, Update, Delete Account
- **Get**: `GET /api/v1/accounts/{account_id}`
- **Update**: `PUT /api/v1/accounts/{account_id}`
- **Delete**: `DELETE /api/v1/accounts/{account_id}`

### 4. List Journal Entries
- **Endpoint**: `/api/v1/journal-entries`
- **Method**: `GET`

### 5. Create Journal Entry
- **Endpoint**: `/api/v1/journal-entries`
- **Method**: `POST`
- **Description**: Must be balanced (Debits = Credits).

**Request Body**
```json
{
  "description": "Initial Capital Inject",
  "currency": "USD",
  "lines": [
    {
      "account_id": 1,
      "debit": 50000.00,
      "credit": 0
    },
    {
      "account_id": 3,
      "debit": 0,
      "credit": 50000.00
    }
  ]
}
```

### 6. Get Journal Entry
- **Endpoint**: `GET /api/v1/journal-entries/{journal_entry_id}`

---

*Documentation generated automatically based on `php artisan route:list` for `api/v1` mappings.*

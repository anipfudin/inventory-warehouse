# API & Endpoints Documentation

## Complete Routes Map

### Authentication Routes (dari Laravel Breeze)
```
GET    /login                          - Show login form
POST   /login                          - Process login
POST   /logout                         - Process logout
GET    /register                       - Show register form
POST   /register                       - Process register
GET    /forgot-password                - Show forgot password form
POST   /forgot-password                - Send password reset link
GET    /reset-password/{token}         - Show reset password form
POST   /reset-password                 - Process password reset
```

### Dashboard
```
GET    /dashboard                      - Dashboard page
       (accessible: auth user | admin + user)
```

### Master Data - Supplier
```
GET    /suppliers                      - List all suppliers
       (accessible: auth user | admin + user)
       Response: Paginated list (10 per page)
       
GET    /suppliers/create               - Show create form
       (accessible: auth user | admin + user)
       
POST   /suppliers                      - Store new supplier
       (accessible: auth user | admin + user)
       Request: name, email, phone, address, city, province, postal_code
       Response: Redirect to index with success message
       
GET    /suppliers/{supplier}           - Show supplier detail
       (accessible: auth user | admin + user)
       Response: Supplier detail page
       
GET    /suppliers/{supplier}/edit      - Show edit form
       (accessible: auth user | admin + user)
       
PUT    /suppliers/{supplier}           - Update supplier
       (accessible: auth user | admin + user)
       Response: Redirect to index with success message
       
DELETE /suppliers/{supplier}           - Delete supplier
       (accessible: auth user | admin + user)
       Constraint: Cannot delete if has active PO
```

### Master Data - Location
```
GET    /locations                      - List all locations
       Response: Paginated list (10 per page)
       
GET    /locations/create               - Show create form

POST   /locations                      - Store new location
       Request: code, name, zone, aisle, rack
       
GET    /locations/{location}           - Show location detail with stocks
       Response: Location detail + stocks breakdown
       
GET    /locations/{location}/edit      - Show edit form

PUT    /locations/{location}           - Update location

DELETE /locations/{location}           - Delete location
```

### Master Data - Item
```
GET    /items                          - List all items
       Response: Paginated list with supplier info
       
GET    /items/create                   - Show create form
       Data: All suppliers list

POST   /items                          - Store new item
       Request: item_number, name, description, unit, supplier_id, 
                unit_price, minimum_stock
       
GET    /items/{item}                   - Show item detail
       Response: Item detail + stocks per location + total stock
       
GET    /items/{item}/edit              - Show edit form

PUT    /items/{item}                   - Update item

DELETE /items/{item}                   - Delete item
```

### Master Data - User (Admin Only)
```
GET    /users                          - List all users
       (accessible: admin only)
       Response: Paginated user list
       
GET    /users/create                   - Show create form
       (accessible: admin only)

POST   /users                          - Store new user
       (accessible: admin only)
       Request: name, email, password, password_confirmation, role
       Validation: email unique, password min 6 chars
       
GET    /users/{user}                   - Show user detail
       (accessible: admin only)
       
GET    /users/{user}/edit              - Show edit form
       (accessible: admin only)

PUT    /users/{user}                   - Update user
       (accessible: admin only)
       Optional: password (jika tidak diisi, tidak di-update)

DELETE /users/{user}                   - Delete user
       (accessible: admin only)
```

### Transaction - Purchase Order (Admin Only)
```
GET    /purchase_orders                - List all PO
       (accessible: admin only)
       Response: PO list with supplier & status
       
GET    /purchase_orders/create         - Show create form
       (accessible: admin only)
       Data: Suppliers list, Items list

POST   /purchase_orders                - Create new PO
       (accessible: admin only)
       Request: supplier_id, delivery_date, notes, items[]
       items[]: item_id, quantity (min 1 item required)
       Response: Redirect to show with success
       Status: draft
       
GET    /purchase_orders/{po}           - Show PO detail
       (accessible: admin only)
       Response: PO header + items + action buttons
       
POST   /purchase_orders/{po}/confirm   - Confirm PO
       (accessible: admin only)
       Workflow: draft → pending
       Validation: Min 1 item in PO
       
POST   /purchase_orders/{po}/receive   - Receive PO & update stock
       (accessible: admin only)
       Workflow: pending → received
       Request: details[] (detail_id, location_id, quantity)
       Action: Update/Create stock, create StockMovement (IN)
       
POST   /purchase_orders/{po}/cancel    - Cancel PO
       (accessible: admin only)
       Workflow: draft/pending → cancelled
```

### Transaction - Sales Order
```
GET    /sales_orders                   - List SO
       (accessible: auth user)
       (user: hanya SO miliknya | admin: semua)
       Response: Paginated SO list
       
GET    /sales_orders/create            - Show create form
       (accessible: auth user)
       Data: Items list

POST   /sales_orders                   - Create new SO
       (accessible: auth user)
       Request: required_date, notes, items[]
       items[]: item_id, quantity (min 1 item required)
       Response: Redirect to show with success
       Status: draft
       created_by: current user
       
GET    /sales_orders/{so}              - Show SO detail
       (accessible: auth user)
       (user: hanya jika pemilik | admin: all)
       Response: SO header + items + action buttons
       
POST   /sales_orders/{so}/confirm      - Confirm SO
       (accessible: auth user)
       Workflow: draft → pending
       Validation: Min 1 item in SO
       
POST   /sales_orders/{so}/ship         - Ship SO & update stock
       (accessible: admin only)
       Workflow: pending → shipped
       Pre-check: Validate all items stock availability
       Error: "Stok tidak cukup untuk item X"
       Action: Reduce stock per location (FIFO), create StockMovement (OUT)
       
POST   /sales_orders/{so}/cancel       - Cancel SO
       (accessible: auth user)
       (user: hanya jika pemilik | admin: all)
```

---

## Response Formats

### Success Response (Index/List)
```json
{
  "data": [
    {
      "id": 1,
      "name": "Item Name",
      "email": "email@example.com",
      ...
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 25,
    "last_page": 3
  }
}
```

### Success Response (Show/Detail)
```json
{
  "data": {
    "id": 1,
    "name": "Item Name",
    "email": "email@example.com",
    ...
  }
}
```

### Error Response
```json
{
  "error": "Error message",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

---

##  Authorization & Middleware

### Middleware Stack
```
1. web
   - Includes: session, cookies, CSRF
   
2. auth (Laravel Breeze)
   - Requires: Authenticated user
   
3. admin (Custom)
   - Requires: auth && user.role == 'admin'
   - Location: app/Http/Middleware/IsAdmin.php
```

### Authorization Checks in Controllers

**SupplierController** - All methods
- Check: User authenticated
- No role check (both admin & user can access)

**ItemController** - All methods
- Check: User authenticated
- No role check (both admin & user can access)

**LocationController** - All methods
- Check: User authenticated
- No role check (both admin & user can access)

**UserController** - All methods
- Check: Admin only (middleware: admin)

**PurchaseOrderController** - All methods
- Check: Admin only (middleware: admin)

**SalesOrderController**
- index: authenticated (filter by created_by if user)
- create: authenticated
- store: authenticated
- show: authenticated + ownership check (403 if user doesn't own)
- confirm: authenticated + ownership check
- cancel: authenticated + ownership check
- ship: admin only

---

## Validation Rules

### Supplier Create/Update
```
name        - required | string | max:255
email       - required | email | unique:suppliers (except self on update)
phone       - required | string | max:20
address     - required | string
city        - required | string | max:100
province    - required | string | max:100
postal_code - required | string | max:10
```

### Item Create/Update
```
item_number    - required | string | unique:items | max:50
name           - required | string | max:255
description    - nullable | string
unit           - required | string | max:50
supplier_id    - required | exists:suppliers,id
unit_price     - required | numeric | min:0
minimum_stock  - required | integer | min:0
```

### Location Create/Update
```
code  - required | string | unique:locations | max:50
name  - required | string | max:255
zone  - nullable | string | max:100
aisle - nullable | string | max:100
rack  - nullable | string | max:100
```

### User Create/Update
```
name     - required | string | max:255
email    - required | email | unique:users (except self on update)
password - required on create | string | min:6 | confirmed
role     - required | in:admin,user
```

### PurchaseOrder Create
```
supplier_id   - required | exists:suppliers,id
delivery_date - nullable | date
notes         - nullable | string
items         - required | array | min:1
items.*.item_id    - required | exists:items,id
items.*.quantity   - required | integer | min:1
```

### SalesOrder Create
```
required_date - nullable | date
notes         - nullable | string
items         - required | array | min:1
items.*.item_id    - required | exists:items,id
items.*.quantity   - required | integer | min:1
```

### PurchaseOrder Receive
```
details                    - required | array
details.*.detail_id        - required | exists:purchase_order_details,id
details.*.location_id      - required | exists:locations,id
details.*.quantity         - required | integer | min:1
```

---

##  Database Relations

### Eloquent Model Relationships

**User**
```php
hasMany(PurchaseOrder::class, 'created_by')
hasMany(SalesOrder::class, 'created_by')
hasMany(StockMovement::class, 'created_by')
```

**Supplier**
```php
hasMany(Item::class)
hasMany(PurchaseOrder::class)
```

**Item**
```php
belongsTo(Supplier::class)
hasMany(Stock::class)
hasMany(PurchaseOrderDetail::class)
hasMany(SalesOrderDetail::class)
hasMany(StockMovement::class)
```

**Location**
```php
hasMany(Stock::class)
hasMany(StockMovement::class)
```

**Stock**
```php
belongsTo(Item::class)
belongsTo(Location::class)
```

**PurchaseOrder**
```php
belongsTo(Supplier::class)
belongsTo(User::class, 'created_by')
hasMany(PurchaseOrderDetail::class)
```

**PurchaseOrderDetail**
```php
belongsTo(PurchaseOrder::class)
belongsTo(Item::class)
```

**SalesOrder**
```php
belongsTo(User::class, 'created_by')
hasMany(SalesOrderDetail::class)
```

**SalesOrderDetail**
```php
belongsTo(SalesOrder::class)
belongsTo(Item::class)
canShip(): bool
```

**StockMovement**
```php
belongsTo(Item::class)
belongsTo(Location::class)
belongsTo(User::class, 'created_by')
```

---

## Business Logic Methods

### Item Model
```php
// Get total stock across all locations
getTotalStock(): int
```

### PurchaseOrder Model
```php
// Generate unique PO number format: PO-202601-0001
generatePoNumber(): string

// Check if PO can be confirmed
canConfirm(): bool

// Check if PO can be received
canReceive(): bool
```

### SalesOrder Model
```php
// Generate unique SO number format: SO-202601-0001
generateSoNumber(): string

// Check if SO can be confirmed
canConfirm(): bool

// Check if SO can be shipped
canShip(): bool

// Check if ALL items have sufficient stock
canShipAll(): bool
```

### SalesOrderDetail Model
```php
// Check if individual item can be shipped
canShip(): bool
```

---

## 🔍 Key Features Implementation

### Stock Validation Logic
```php
// In SalesOrderController::ship()
foreach ($salesOrder->details as $detail) {
    if (!$detail->canShip()) {
        return back()->with('error', 'Stok tidak cukup untuk item ' . $detail->item->name);
    }
}
```

### Stock Update on PO Receive
```php
// In PurchaseOrderController::receive()
$stock = Stock::where('item_id', $podDetail->item_id)
    ->where('location_id', $detail['location_id'])
    ->first();

if ($stock) {
    $stock->update(['quantity' => $stock->quantity + $detail['quantity']]);
} else {
    Stock::create([...]);
}

StockMovement::create([
    'reference_number' => $purchaseOrder->po_number,
    'reference_type' => 'PURCHASE_ORDER',
    'type' => 'IN',
    ...
]);
```

### Stock Reduction on SO Ship (FIFO)
```php
// In SalesOrderController::ship()
foreach ($salesOrder->details as $detail) {
    $remainingQuantity = $detail->quantity_requested;
    
    // Get stocks ordered by oldest first (FIFO)
    $stocks = Stock::where('item_id', $detail->item_id)
        ->where('quantity', '>', 0)
        ->orderBy('created_at')
        ->get();
    
    foreach ($stocks as $stock) {
        if ($remainingQuantity <= 0) break;
        
        $quantityToTake = min($remainingQuantity, $stock->quantity);
        $stock->update(['quantity' => $stock->quantity - $quantityToTake]);
        
        StockMovement::create([
            'type' => 'OUT',
            ...
        ]);
        
        $remainingQuantity -= $quantityToTake;
    }
}
```

---

## Performance Optimizations

### Query Optimizations
- Use `with()` untuk eager loading relationships
- Paginate large datasets (default 10 per page)
- Index frequently queried columns

### Caching Opportunities
- Cache dashboard statistics
- Cache supplier list (if not frequent changes)
- Cache location list

### Database Indexes
```sql
-- Recommended indexes
item_id, location_id (stocks table)
created_by (purchase_orders, sales_orders, stock_movements)
reference_number (stock_movements)
status (purchase_orders, sales_orders)
```

---

**Last Updated**: January 3, 2026

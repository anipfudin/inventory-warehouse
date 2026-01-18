# Inventory & Warehouse Management System

Sistem Manajemen Inventory dan Warehouse yang dibangun dengan Laravel 12, PHP 8.2, dan MySQL. Sistem ini memiliki fitur lengkap untuk mengelola supplier, barang, lokasi stok, serta transaksi barang masuk dan keluar dengan validasi stok.

## 🎯 Fitur Utama

### Master Data
- **Supplier Management**: CRUD data supplier dengan informasi lengkap (nama, email, phone, alamat)
- **Item/Barang Management**: CRUD data barang dengan nomor item unik, harga satuan, dan minimum stok
- **Location/Lokasi**: CRUD lokasi penyimpanan barang (Zone, Aisle, Rack)
- **User Management**: CRUD user dengan role-based access (Admin dan User)

### Transaksi Barang
- **Purchase Order (Barang Masuk)**:
  - Membuat PO dari supplier
  - Konfirmasi PO (draft → pending)
  - Terima barang dan update stok otomatis ke lokasi tertentu
  - Cancel PO jika diperlukan
  - Hanya Admin yang bisa mengelola

- **Sales Order (Barang Keluar)**:
  - Membuat SO untuk pengiriman
  - Konfirmasi SO (draft → pending)
  - **Validasi Stok**: Barang TIDAK bisa keluar jika permintaan > stok tersedia
  - Shipping barang dengan update stok otomatis (FIFO)
  - Admin mengelola shipping, User bisa membuat dan confirm SO

### Role-Based Access Control
- **Admin**:
  - ✅ Akses penuh ke semua master data
  - ✅ Mengelola barang masuk (PO) dan barang keluar (SO)
  - ✅ Mengelola user
  - ✅ Melihat laporan dan statistik
  - ✅ Melakukan shipping barang keluar

- **User**:
  - ✅ Membuat dan confirm Sales Order
  - ❌ Tidak bisa mengelola Purchase Order
  - ❌ Tidak bisa melakukan shipping
  - ✅ Akses view ke master data

### Stock Management
- Real-time stock tracking per lokasi
- Stock movement history (IN/OUT)
- Validasi otomatis stok saat pengiriman
- Alert untuk stok yang kurang dari minimum
- Tracking qty di setiap lokasi (Zone, Aisle, Rack)

### Dashboard
- Total statistik (Item, Supplier, Lokasi, Total Stock Value)
- Pending transactions (PO & SO)
- Recent stock movements dengan user dan tipe (IN/OUT)
- Low stock items alert

## 🏗️ Arsitektur & Database

### Model-View-Controller (MVC)
Struktur MVC yang clean dan scalable:
- **Models**: Eloquent ORM dengan relationships
- **Controllers**: Resource controllers dengan business logic
- **Views**: Blade templates responsive dengan Bootstrap 5

### Database Schema

**Master Tables:**
- `users` - User dengan role (admin/user)
- `suppliers` - Data supplier lengkap
- `locations` - Lokasi penyimpanan (Zone/Aisle/Rack)
- `items` - Data barang dengan harga & minimum stok
- `stocks` - Qty barang per lokasi

**Transaction Tables:**
- `purchase_orders` - Header PO dari supplier
- `purchase_order_details` - Detail item dalam PO
- `sales_orders` - Header SO untuk pengiriman
- `sales_order_details` - Detail item dalam SO dengan qty requested/shipped
- `stock_movements` - History pergerakan stok (IN/OUT) dengan reference

## 🚀 Instalasi & Setup

### Prerequisites
- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js & npm
- Git

### Langkah Instalasi

1. **Setup project**:
   ```bash
   cd inventory-warehouse
   ```

2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Setup environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi database** di `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=warehouse
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations & seeding**:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build frontend assets**:
   ```bash
   npm run build
   ```

7. **Start server**:
   ```bash
   php artisan serve
   ```
   Server akan running di `http://127.0.0.1:8000`

## 👤 Akun Default (Setelah Seeding)

### Admin Account
- **Email**: `admin@inventory.com`
- **Password**: `password`
- **Role**: Admin

### User Account
- **Email**: `user@inventory.com`
- **Password**: `password`
- **Role**: User

Setelah login, bisa buat user baru melalui menu Users (Admin only).

## 📍 Routes & URL Mapping

### Public Routes
```
GET  /                    - Welcome page
GET  /login               - Login form
POST /login               - Process login
GET  /register            - Register form
POST /register            - Process register
POST /forgot-password     - Forgot password
```

### Protected Routes (Authenticated)
```
GET  /dashboard           - Dashboard

GET  /suppliers           - List suppliers
GET  /suppliers/create    - Create form
POST /suppliers           - Store supplier
GET  /suppliers/{id}      - Show detail
GET  /suppliers/{id}/edit - Edit form
PUT  /suppliers/{id}      - Update supplier
DELETE /suppliers/{id}    - Delete supplier

GET  /locations           - List locations
GET  /locations/create    - Create form
POST /locations           - Store location
GET  /locations/{id}      - Show detail
GET  /locations/{id}/edit - Edit form
PUT  /locations/{id}      - Update location
DELETE /locations/{id}    - Delete location

GET  /items               - List items
GET  /items/create        - Create form
POST /items               - Store item
GET  /items/{id}          - Show detail with stocks
GET  /items/{id}/edit     - Edit form
PUT  /items/{id}          - Update item
DELETE /items/{id}        - Delete item

GET  /sales_orders        - List SO (user: hanya SO miliknya)
GET  /sales_orders/create - Create form
POST /sales_orders        - Store SO
GET  /sales_orders/{id}   - Show detail
POST /sales_orders/{id}/confirm - Confirm SO
POST /sales_orders/{id}/cancel  - Cancel SO
```

### Admin Only Routes
```
GET  /users               - List users
GET  /users/create        - Create form
POST /users               - Store user
GET  /users/{id}          - Show detail
GET  /users/{id}/edit     - Edit form
PUT  /users/{id}          - Update user
DELETE /users/{id}        - Delete user

GET  /purchase_orders     - List PO
GET  /purchase_orders/create - Create form
POST /purchase_orders     - Store PO
GET  /purchase_orders/{id} - Show detail
POST /purchase_orders/{id}/confirm - Confirm PO
POST /purchase_orders/{id}/receive - Receive PO & update stock
POST /purchase_orders/{id}/cancel  - Cancel PO

POST /sales_orders/{id}/ship - Ship SO (Admin only, triggers stock update)
```

## 💼 Business Logic & Workflow

### Purchase Order (Barang Masuk) Workflow

```
1. CREATE PO (Draft)
   ├─ Select Supplier
   ├─ Add Items dengan quantity
   └─ Set delivery date (optional)

2. CONFIRM PO (Draft → Pending)
   └─ Validasi: ada minimal 1 item

3. RECEIVE PO (Pending)
   ├─ Select lokasi untuk setiap item
   ├─ Confirm qty yang diterima
   ├─ Update Stock:
   │  ├─ Cek apakah stock sudah ada di lokasi
   │  ├─ Jika ada → add quantity
   │  └─ Jika tidak ada → create stock baru
   └─ Create StockMovement record (IN)

4. STATUS MENJADI RECEIVED
   └─ Stok sudah tersedia di warehouse
```

### Sales Order (Barang Keluar) Workflow

```
1. CREATE SO (Draft)
   ├─ Add Items dengan quantity_requested
   └─ Set required date (optional)

2. CONFIRM SO (Draft → Pending)
   └─ Validasi: ada minimal 1 item

3. VALIDATE STOCK (sebelum shipping)
   ├─ Hitung total stok dari semua lokasi: getTotalStock()
   ├─ Untuk setiap item: if (totalStock < quantityRequested)
   │  └─ ❌ INVALID - Tidak bisa di-ship
   │     Error: "Stok tidak cukup untuk item X"
   └─ Semua item valid → Bisa di-ship

4. SHIP SO (Pending → Shipped) ⚠️ ADMIN ONLY
   ├─ Loop setiap item dalam SO
   ├─ Ambil stok dari lokasi (FIFO)
   │  ├─ Cari semua stocks item yang qty > 0
   │  ├─ Ambil dari stok pertama sesuai qty dibutuhkan
   │  ├─ Kurangi qty di stock location
   │  └─ Jika qty masih kurang, ambil dari stock berikutnya
   ├─ Create StockMovement record (OUT) untuk setiap lokasi
   └─ Update SalesOrderDetail: quantity_shipped = quantity_requested

5. STATUS MENJADI SHIPPED
   └─ Barang sudah keluar dari warehouse
```

### Stock Validation Logic (Key Feature)

```php
// Di SalesOrder Model
public function canShipAll(): bool
{
    foreach ($this->details as $detail) {
        $totalStock = $detail->item->getTotalStock();
        if ($totalStock < $detail->quantity_requested) {
            return false;  // ❌ Tidak bisa ship
        }
    }
    return true;  // ✅ Bisa ship
}

// Di SalesOrderDetail Model
public function canShip(): bool
{
    $availableStock = $this->item->getTotalStock();
    return $availableStock >= $this->quantity_requested;
}

// Di Item Model
public function getTotalStock(): int
{
    return $this->stocks()->sum('quantity');
}
```

## 📁 File Structure

```
inventory-warehouse/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php       # Dashboard logic
│   │   │   ├── SupplierController.php        # CRUD Supplier
│   │   │   ├── LocationController.php        # CRUD Location
│   │   │   ├── ItemController.php            # CRUD Item
│   │   │   ├── UserController.php            # CRUD User (Admin)
│   │   │   ├── PurchaseOrderController.php   # Barang Masuk
│   │   │   └── SalesOrderController.php      # Barang Keluar
│   │   ├── Middleware/
│   │   │   ├── IsAdmin.php                   # Admin check middleware
│   │   │   └── Authenticate.php              # Auth middleware
│   │   └── Requests/                         # Form Requests
│   │
│   └── Models/
│       ├── User.php                          # User model + roles
│       ├── Supplier.php                      # Supplier model
│       ├── Location.php                      # Location model
│       ├── Item.php                          # Item model
│       ├── Stock.php                         # Stock model (qty per lokasi)
│       ├── PurchaseOrder.php                 # PO model
│       ├── PurchaseOrderDetail.php           # PO detail model
│       ├── SalesOrder.php                    # SO model
│       ├── SalesOrderDetail.php              # SO detail model
│       └── StockMovement.php                 # Stock movement history
│
├── database/
│   ├── migrations/
│   │   ├── 2026_01_03_000000_add_role_to_users_table.php
│   │   ├── 2026_01_03_000001_create_suppliers_table.php
│   │   ├── 2026_01_03_000002_create_locations_table.php
│   │   ├── 2026_01_03_000003_create_items_table.php
│   │   ├── 2026_01_03_000004_create_stocks_table.php
│   │   ├── 2026_01_03_000005_create_purchase_orders_table.php
│   │   ├── 2026_01_03_000006_create_purchase_order_details_table.php
│   │   ├── 2026_01_03_000007_create_sales_orders_table.php
│   │   ├── 2026_01_03_000008_create_sales_order_details_table.php
│   │   └── 2026_01_03_000009_create_stock_movements_table.php
│   │
│   └── seeders/
│       ├── UserSeeder.php                    # Seed default users (admin, user)
│       ├── SupplierSeeder.php                # Seed sample suppliers
│       ├── LocationSeeder.php                # Seed sample locations
│       ├── ItemSeeder.php                    # Seed sample items
│       └── DatabaseSeeder.php                # Main seeder
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php                 # Master layout dengan sidebar
│       │
│       ├── dashboard.blade.php               # Dashboard page
│       │
│       ├── suppliers/
│       │   ├── index.blade.php               # List suppliers
│       │   ├── create.blade.php              # Create form
│       │   ├── edit.blade.php                # Edit form
│       │   └── show.blade.php                # Detail view
│       │
│       ├── locations/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── show.blade.php
│       │
│       ├── items/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── show.blade.php                # Show dengan stock per lokasi
│       │
│       ├── users/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── show.blade.php
│       │
│       ├── purchase-orders/
│       │   ├── index.blade.php               # List PO
│       │   ├── create.blade.php              # Create form
│       │   ├── edit.blade.php
│       │   └── show.blade.php                # Detail + receive modal
│       │
│       ├── sales-orders/
│       │   ├── index.blade.php               # List SO
│       │   ├── create.blade.php              # Create form
│       │   ├── edit.blade.php
│       │   └── show.blade.php                # Detail + ship modal
│       │
│       └── auth/                             # Laravel Breeze auth views
│           ├── login.blade.php
│           ├── register.blade.php
│           ├── forgot-password.blade.php
│           └── reset-password.blade.php
│
├── routes/
│   ├── web.php                               # Web routes dengan middleware
│   ├── auth.php                              # Auth routes (Breeze)
│   └── api.php                               # API routes
│
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   └── ...
│
└── public/
    └── build/                                # Vite build assets
```

## 🛠️ Teknologi Stack

| Kategori | Teknologi |
|----------|-----------|
| **Backend** | Laravel 12, PHP 8.2 |
| **Database** | MySQL 8.0 |
| **Frontend** | Blade Templates, Bootstrap 5, Alpine.js |
| **Build Tool** | Vite |
| **Package Manager** | Composer, npm |
| **Authentication** | Laravel Breeze |
| **ORM** | Eloquent |
| **Icons** | Bootstrap Icons |

## 🔧 Development Commands

```bash
# Start development server
php artisan serve --port=8000

# Run database migrations
php artisan migrate

# Seed database dengan data dummy
php artisan db:seed

# Reset & seed database (WARNING: delete all data!)
php artisan migrate:refresh --seed

# Tinker (PHP REPL)
php artisan tinker

# Build frontend assets
npm run build

# Watch file changes
npm run dev

# Running tests
php artisan test

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Generate Laravel docs
php artisan scribe:generate
```

## 🐛 Troubleshooting

### Database Connection Error
```bash
# Check .env file settings
# Make sure MySQL is running
# Create database manually jika diperlukan:
mysql -u root -p
> CREATE DATABASE warehouse;
```

### Permission Issues
```bash
# Linux/Mac
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Windows (Run as Administrator)
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Reset Everything
```bash
# Reset database dan seed ulang
php artisan migrate:refresh --seed

# Rebuild frontend
npm run build
```

## 📊 Database Relationships

```
User
├─ PurchaseOrders (hasMany, created_by)
└─ SalesOrders (hasMany, created_by)

Supplier
├─ Items (hasMany)
└─ PurchaseOrders (hasMany)

Item
├─ Stocks (hasMany)
├─ Supplier (belongsTo)
├─ PurchaseOrderDetails (hasMany)
├─ SalesOrderDetails (hasMany)
└─ StockMovements (hasMany)

Location
├─ Stocks (hasMany)
└─ StockMovements (hasMany)

Stock
├─ Item (belongsTo)
└─ Location (belongsTo)

PurchaseOrder
├─ Supplier (belongsTo)
├─ Details (hasMany)
└─ CreatedBy (belongsTo User)

SalesOrder
├─ Details (hasMany)
└─ CreatedBy (belongsTo User)

StockMovement
├─ Item (belongsTo)
├─ Location (belongsTo)
└─ CreatedBy (belongsTo User)
```

## 📝 Notes

- **Stock Validation**: Sistem otomatis mengecek stok sebelum pengiriman
- **FIFO Method**: Stock diambil dari lokasi yang paling lama (First In First Out)
- **Audit Trail**: Semua pergerakan stok tercatat di `stock_movements` table
- **Role-Based**: User regular hanya bisa membuat SO, tidak bisa manage PO
- **Number Generation**: PO dan SO number auto-generated: `PO-202601-0001`, `SO-202601-0001`

## 📞 Support

Untuk pertanyaan atau issue, silakan buka issue di repository atau hubungi developer.

---

**Built with Laravel ❤️ | Inventory Warehouse Management System**

# Quick Start Guide - Inventory Warehouse System

## ⚡ Setup Cepat (5 menit)

### 1. Install Dependencies
```bash
cd inventory-warehouse
composer install
npm install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure Database
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=warehouse
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run Migrations & Seed
```bash
php artisan migrate
php artisan db:seed
```

### 5. Build Assets & Start Server
```bash
npm run build
php artisan serve
```

Buka browser: **http://127.0.0.1:8000**

---

## 🔐 Login

### Admin Account
- Email: `admin@inventory.com`
- Password: `password`

### User Account
- Email: `user@inventory.com`
- Password: `password`

---

## 📋 Fitur Utama - Quick Tour

### 1️⃣ Dashboard
- Lihat statistik total item, supplier, lokasi
- Lihat total nilai stok
- Lihat transaksi pending
- Lihat stock movements terbaru
- Alert stok rendah

**Akses**: Semua user → `/dashboard`

### 2️⃣ Master Data

#### Supplier Management
- Tambah supplier baru
- Edit data supplier
- Hapus supplier (jika tidak ada PO aktif)
- Lihat detail supplier

**Akses**: Semua user → `/suppliers`

#### Item Management
- Tambah item dengan nomor unik, harga, minimum stok
- Edit item data
- Lihat total stok per item
- Lihat breakdown stok per lokasi

**Akses**: Semua user → `/items`

#### Location Management
- Tambah lokasi penyimpanan (Zone/Aisle/Rack)
- Edit lokasi
- Lihat stok barang di lokasi

**Akses**: Semua user → `/locations`

#### User Management (Admin Only)
- Buat user baru dengan role (admin/user)
- Edit user
- Hapus user

**Akses**: Admin only → `/users`

### 3️⃣ Transaksi Barang

#### Purchase Order - Barang Masuk 📥 (Admin Only)
**Workflow:**
1. Click "Barang Masuk" → `/purchase_orders`
2. Click "Tambah PO" → `/purchase_orders/create`
3. Pilih supplier
4. Tambah item + qty
5. Set delivery date (optional)
6. Click "Simpan" → PO dibuat (status: draft)
7. Click "Konfirmasi" → Status berubah ke pending
8. Click "Terima Barang" → Pilih lokasi untuk setiap item
9. PO received → Stok otomatis terupdate

**Demo Flow:**
```
Supplier: PT. Supplier Jaya
Item: Resistor 1K Ohm (100 pcs)
Lokasi: A1
Delivery: 2026-01-10

Hasil: Stock bertambah 100 pcs di lokasi A1
```

#### Sales Order - Barang Keluar 📤
**Workflow:**
1. Click "Barang Keluar" → `/sales_orders`
2. Click "Tambah SO" → `/sales_orders/create`
3. Tambah item + qty yang diminta
4. Set required date (optional)
5. Click "Simpan" → SO dibuat (status: draft)
6. Click "Konfirmasi" → Status berubah ke pending
7. ⚠️ **VALIDASI STOK**: Sistem cek apakah stok cukup
   - Jika stok < qty diminta → Error ❌
   - Jika stok >= qty diminta → Lanjut ✅
8. (Admin) Click "Kirim Barang" → Stok otomatis berkurang
9. SO shipped → Barang keluar selesai

**Demo Flow:**
```
Item: LED Merah 5mm (150 pcs diminta)
Current Stock: 200 pcs (A1: 100, B1: 100)

Hasil: Stock berkurang
- A1: 100 → 0 pcs
- B1: 100 → 50 pcs
Total stock: 50 pcs tersisa
```

---

## ⚙️ Key Features Explained

### ✅ Stock Validation
**Sistem otomatis cek stok sebelum barang keluar:**

```
Jika qty diminta > total stok → ❌ TIDAK BISA KIRIM
Error: "Stok tidak cukup untuk item X"

Jika qty diminta <= total stok → ✅ BISA KIRIM
```

Contoh:
- Item: Barang A
- Total stok: 50 pcs (A1: 30 pcs + B1: 20 pcs)
- Permintaan: 60 pcs
- Hasil: ❌ TIDAK BISA (perlu 10 pcs lagi)

### 📊 Stock Tracking
Setiap barang masuk/keluar tercatat di **Stock Movements**:
- Reference number (PO/SO number)
- Jenis transaksi (IN/OUT)
- Item & lokasi
- Qty
- User yang input
- Timestamp

### 👥 Role-Based Access
| Menu | Admin | User |
|------|-------|------|
| Dashboard | ✅ | ✅ |
| Supplier | ✅ | ✅ |
| Item | ✅ | ✅ |
| Location | ✅ | ✅ |
| Barang Masuk (PO) | ✅ | ❌ |
| Barang Keluar (SO) - Create | ✅ | ✅ |
| Barang Keluar (SO) - Ship | ✅ | ❌ |
| User Management | ✅ | ❌ |

---

## 📱 Interface Overview

### Sidebar Navigation
```
📊 Dashboard
├─ Master Data
│  ├─ 🏢 Supplier
│  ├─ 📦 Item/Barang
│  └─ 📍 Lokasi
├─ Transaksi (Admin)
│  ├─ 📥 Barang Masuk (PO)
│  └─ 📤 Barang Keluar (SO)
├─ Setting (Admin)
│  └─ 👥 User
└─ Account
   └─ Logout
```

### Color Scheme
- Sidebar: Dark blue (#2c3e50)
- Accent: Light blue (#3498db)
- Success: Green (#2ecc71)
- Warning: Yellow (#f39c12)
- Danger: Red (#e74c3c)

---

## 🧪 Test Scenarios

### Scenario 1: Penerimaan Barang
```
1. Login sebagai Admin
2. Buat PO ke "PT. Supplier Jaya"
3. Tambah: 200x Resistor 1K
4. Konfirmasi PO
5. Terima barang ke lokasi A1
6. Cek Dashboard → Stock value meningkat
```

### Scenario 2: Pengiriman Barang (Sukses)
```
1. Login sebagai User
2. Buat SO untuk 100x LED Merah 5mm
3. Konfirmasi SO
4. (Admin) Ship barang
5. Cek Stock → Berkurang 100 pcs
```

### Scenario 3: Pengiriman Barang (Gagal - Stok Kurang)
```
1. Login sebagai User
2. Buat SO untuk 300x LED Merah 5mm (total stok hanya 200)
3. Konfirmasi SO
4. (Admin) Coba Ship → ❌ ERROR
   "Stok tidak cukup untuk item LED Merah 5mm"
5. User harus batalkan atau request stok tambahan
```

### Scenario 4: Multiple Locations
```
1. Admin buat PO: 100x Item A
2. Terima ke lokasi A1: 50 pcs
3. Terima ke lokasi B1: 50 pcs
4. User buat SO: 80x Item A
5. Admin ship: 
   - Ambil dari A1: 50 pcs
   - Ambil dari B1: 30 pcs
   - Sisa: A1: 0 pcs, B1: 20 pcs
```

---

## 🔍 Data Entry Example

### Creating a Supplier
```
Nama: PT. Elektronik Jaya
Email: contact@elektronikjaya.com
Phone: 021-12345678
Address: Jl. Mawar No. 123
City: Jakarta
Province: DKI Jakarta
Postal Code: 12345
```

### Creating an Item
```
Item Number: ITM-LED-001
Name: LED Merah 5mm
Description: LED 5mm Merah Bright 20mA
Unit: pcs
Supplier: PT. Elektronik Jaya
Unit Price: Rp 2.000
Minimum Stock: 200 pcs
```

### Creating a Location
```
Code: A1
Name: Area A - Rak 1
Zone: A
Aisle: 1
Rack: 1
```

---

## 🐛 Common Issues & Solutions

### Issue: Database tidak terkoneksi
**Solution:**
```bash
# Cek .env file
# Pastikan MySQL running
# Jalankan: php artisan migrate
```

### Issue: View file not found
**Solution:**
```bash
# Clear cache
php artisan view:clear

# Rebuild assets
npm run build
```

### Issue: Permission denied pada storage
**Solution:**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### Issue: Forgot to seed database
**Solution:**
```bash
php artisan db:seed
# Atau lengkap reset:
php artisan migrate:refresh --seed
```

---

## 📞 Useful Commands

```bash
# Development
php artisan serve --port=8000
npm run dev

# Database
php artisan migrate
php artisan migrate:refresh --seed
php artisan db:seed

# Debugging
php artisan tinker
php artisan make:migration [name]
php artisan make:model [name]

# Cache Clear
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Production
php artisan optimize
npm run build
```

---

## 📖 Next Steps

1. **Explore Dashboard** - Lihat overview sistem
2. **Create Sample Data**:
   - Buat 2-3 supplier
   - Buat 5-10 items
   - Buat 3-4 locations
3. **Test Full Flow**:
   - Create PO → Receive → Lihat stock naik
   - Create SO → Confirm → Ship → Lihat stock turun
4. **Monitor Stock Movements** - Cek history di dashboard

---

**Selamat menggunakan Inventory Warehouse System! 🎉**

Untuk dokumentasi lengkap, lihat file `DOKUMENTASI.md`

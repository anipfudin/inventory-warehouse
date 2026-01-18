# Testing & Validation Guide

## 🧪 Manual Testing Scenarios

### Pre-Testing Checklist
- [ ] Database migrasi berhasil
- [ ] Seeding data berhasil
- [ ] Server running di port 8000
- [ ] Assets built (npm run build)
- [ ] Browser: Chrome/Firefox/Safari

---

## 📋 Test Case 1: User Login & Authentication

### Objective
Verify authentication system bekerja dengan benar

### Test Steps
1. Buka `http://127.0.0.1:8000`
2. Click "Login"
3. Input email: `admin@inventory.com`, password: `password`
4. Click "Sign in"

### Expected Result
- ✅ Login berhasil
- ✅ Redirect ke dashboard
- ✅ User info menunjukkan "Admin User (admin)"
- ✅ Sidebar menampilkan semua menu (Supplier, Item, Location, PO, SO, User)

### Test Alternative
- Coba login dengan user: `user@inventory.com`
- ✅ Hanya sidebar yang berbeda (tidak ada PO dan User menu)

---

## 📋 Test Case 2: Master Data - Supplier Management

### Objective
Test CRUD operations pada Supplier

### Create Supplier
1. Login sebagai Admin
2. Click "Supplier" di sidebar
3. Click "Tambah Supplier"
4. Fill form:
   ```
   Nama: CV. Test Supplier
   Email: test.supplier@example.com
   Phone: 0812-3456789
   Address: Jl. Test No. 123
   City: Jakarta
   Province: DKI Jakarta
   Postal Code: 12345
   ```
5. Click "Simpan"

**Expected Result:**
- ✅ Success message ditampilkan
- ✅ Redirect ke list supplier
- ✅ Data baru muncul di list
- ✅ Dapat lihat detail dengan click baris

### Read/List Supplier
1. Click "Supplier"
2. Lihat list supplier

**Expected Result:**
- ✅ Minimal 4 supplier muncul (3 dari seeding + 1 baru)
- ✅ Pagination working (jika > 10)
- ✅ Info: nama, email, phone, city ditampilkan

### Update Supplier
1. Click "Edit" pada supplier test
2. Edit nama menjadi: `CV. Test Supplier Updated`
3. Click "Update"

**Expected Result:**
- ✅ Success message
- ✅ Data terupdate di list

### Delete Supplier
1. Click "Delete" pada supplier test
2. Confirm dialog
3. Click "OK"

**Expected Result:**
- ✅ Success message
- ✅ Data hilang dari list

---

## 📋 Test Case 3: Master Data - Item Management

### Objective
Test CRUD operations pada Item

### Create Item
1. Go to Items → "Tambah Item"
2. Fill form:
   ```
   Item Number: ITM-TEST-001
   Name: Test Component
   Description: Component untuk testing
   Unit: pcs
   Supplier: PT. Supplier Jaya
   Unit Price: 5000
   Minimum Stock: 50
   ```
3. Click "Simpan"

**Expected Result:**
- ✅ Success message
- ✅ Item muncul di list
- ✅ Total stock menunjukkan 0 (belum ada stok)

### View Item Detail
1. Click item yang baru dibuat
2. View detail page

**Expected Result:**
- ✅ Menampilkan semua info item
- ✅ Section "Stock per Location" kosong (belum ada stok)
- ✅ Total Stock: 0

---

## 📋 Test Case 4: Location Management

### Objective
Test CRUD location

### Create Location
1. Go to Locations → "Tambah Lokasi"
2. Fill form:
   ```
   Code: D1
   Name: Area D - Rak 1
   Zone: D
   Aisle: 4
   Rack: 1
   ```
3. Click "Simpan"

**Expected Result:**
- ✅ Berhasil dibuat
- ✅ Muncul di list

---

## 📋 Test Case 5: User Management (Admin Only)

### Objective
Test user creation dan role assignment

### Create User
1. Login sebagai Admin
2. Go to Users → "Tambah User"
3. Fill form:
   ```
   Name: Test User
   Email: testuser@inventory.com
   Password: password123
   Confirm Password: password123
   Role: user
   ```
4. Click "Simpan"

**Expected Result:**
- ✅ User berhasil dibuat
- ✅ Muncul di list
- ✅ Role menunjukkan "user"

### Test User Login
1. Logout dari admin
2. Login dengan user baru: `testuser@inventory.com` / `password123`
3. Check sidebar

**Expected Result:**
- ✅ Login berhasil
- ✅ Sidebar TIDAK menampilkan "Barang Masuk (PO)" menu
- ✅ Sidebar TIDAK menampilkan "User" menu
- ✅ Hanya "Barang Keluar (SO)" yang bisa diakses untuk transaksi

---

## 📋 Test Case 6: Purchase Order - Barang Masuk (Admin Only)

### Objective
Test PO workflow: Create → Confirm → Receive

### Create PO
1. Login sebagai Admin
2. Go to "Barang Masuk (PO)" → "Tambah PO"
3. Fill form:
   ```
   Supplier: PT. Supplier Jaya
   Delivery Date: 2026-01-15
   Notes: Test PO untuk testing
   
   Items:
   - Resistor 1K Ohm (Item ID 1): 100 pcs
   - Kapasitor 10uF (Item ID 2): 50 pcs
   ```
4. Click "Simpan"

**Expected Result:**
- ✅ PO berhasil dibuat
- ✅ Status: draft
- ✅ PO Number auto-generated: `PO-202601-XXXX`
- ✅ Total Amount ter-calculate: (100 × 500) + (50 × 1500) = 125,000
- ✅ Menampilkan button "Konfirmasi"

### Confirm PO
1. Click "Konfirmasi" button
2. Confirm dialog

**Expected Result:**
- ✅ Status berubah menjadi "pending"
- ✅ Button berubah menjadi "Terima Barang"
- ✅ Tidak bisa edit PO lagi

### Receive PO & Update Stock
1. Click "Terima Barang"
2. Dialog/modal muncul
3. Fill lokasi untuk setiap item:
   ```
   Item: Resistor 1K Ohm
   Location: A1
   Qty: 100
   
   Item: Kapasitor 10uF
   Location: A2
   Qty: 50
   ```
4. Click "Terima"

**Expected Result:**
- ✅ Status berubah menjadi "received"
- ✅ Success message ditampilkan
- ✅ Stock diupdate di location A1 dan A2

### Verify Stock Updated
1. Go to Items → "Resistor 1K Ohm"
2. View detail

**Expected Result:**
- ✅ Total Stock: 100 pcs
- ✅ Stock per Location: A1: 100 pcs
- ✅ Status "Received" di PO detail

---

## 📋 Test Case 7: Sales Order - Barang Keluar

### Objective
Test SO workflow dengan stock validation

### Create SO (User)
1. Logout admin, login sebagai user: `user@inventory.com`
2. Go to "Barang Keluar (SO)" → "Tambah SO"
3. Fill form:
   ```
   Required Date: 2026-01-20
   Notes: Test SO
   
   Items:
   - Resistor 1K Ohm: 50 pcs
   - Kapasitor 10uF: 25 pcs
   ```
4. Click "Simpan"

**Expected Result:**
- ✅ SO berhasil dibuat
- ✅ Status: draft
- ✅ SO Number auto-generated
- ✅ Created by: "Regular User"
- ✅ Button "Konfirmasi" tersedia

### Confirm SO
1. Click "Konfirmasi"

**Expected Result:**
- ✅ Status: pending
- ✅ Button berubah (jika user: hanya "Batalkan")

### Verify Stock Validation (Login Admin)
1. Logout, login sebagai admin
2. Go to SO yang dibuat
3. Check stock validation message

**Expected Result:**
- ✅ Message: "Stok tersedia: Resistor 1K: 100 pcs ✅ Kapasitor 10uF: 50 pcs ✅"
- ✅ Button "Kirim Barang" tersedia

### Ship SO (Admin)
1. Click "Kirim Barang"
2. Confirm dialog

**Expected Result:**
- ✅ Status berubah: pending → shipped
- ✅ Stock berkurang di location
- ✅ Success message
- ✅ StockMovement tercatat

### Verify Stock Reduced
1. Go to Items → "Resistor 1K Ohm"
2. View detail

**Expected Result:**
- ✅ Total Stock berkurang: 100 - 50 = 50 pcs
- ✅ Stock di A1 berkurang: 100 - 50 = 50 pcs

---

## 📋 Test Case 8: Stock Validation - Negative Case

### Objective
Test validasi stok: **Barang TIDAK bisa keluar jika stok kurang**

### Setup
1. Admin buat PO dengan stok terbatas
2. Terima PO: 20 pcs Resistor 1K di lokasi A1

### Test
1. User buat SO: 30 pcs Resistor 1K (minta lebih dari stok: 20)
2. Confirm SO
3. Admin coba Ship

**Expected Result:**
- ❌ ERROR message: "Stok tidak cukup untuk item Resistor 1K Ohm"
- ❌ Tombol Ship disabled atau tidak berfungsi
- SO tetap status "pending"
- Stock tidak berkurang

### Verification
1. Check dashboard → SO pending masih tertampil
2. Check item stock → Tetap 20 pcs (tidak berkurang)

---

## 📋 Test Case 9: Stock Movement Tracking

### Objective
Verify stock movement history tercatat dengan benar

### Check Stock Movements
1. Go to Dashboard
2. Scroll ke "Recent Stock Movements"

**Expected Result:**
- ✅ Terlihat history pergerakan stok
- ✅ Kolom: Reference, Type (IN/OUT), Item, Qty, Location, User, Date
- ✅ IN dari PO yang diterima
- ✅ OUT dari SO yang dikirim

---

## 📋 Test Case 10: Multiple Locations - FIFO Logic

### Objective
Test FIFO (First In First Out) saat mengambil stok dari berbagai lokasi

### Setup
1. Admin buat PO 1: 100 pcs Item di lokasi A1
2. Terima PO 1 di lokasi A1
3. Admin buat PO 2: 100 pcs Item di lokasi B1 (dengan date lebih belakangan)
4. Terima PO 2 di lokasi B1
5. Total stok: A1: 100 + B1: 100 = 200 pcs

### Test
1. User buat SO: 150 pcs Item
2. Confirm & Admin ship

**Expected Result (FIFO):**
- ✅ Ambil dari A1 dulu (oldest): 100 pcs → A1 jadi 0
- ✅ Ambil dari B1 (newest): 50 pcs → B1 jadi 50
- ✅ Total stok: 50 pcs
- ✅ StockMovement ada 2 record (OUT dari A1 dan B1)

---

## 📋 Test Case 11: Authorization & Role Checking

### Objective
Verify user tidak bisa akses resource yang tidak diizinkan

### Test as Regular User
1. Login sebagai user: `user@inventory.com`
2. Coba akses URL langsung:
   - `/purchase_orders` → Should 403 Forbidden
   - `/users` → Should 403 Forbidden
   - `/users/create` → Should 403 Forbidden

**Expected Result:**
- ❌ Access denied / 403 error
- ❌ Sidebar tidak menampilkan menu tersebut

### Test as Admin
1. Login sebagai admin
2. Akses URL yang sama

**Expected Result:**
- ✅ Akses berhasil
- ✅ Halaman ditampilkan normal

---

## 📋 Test Case 12: Dashboard Statistics

### Objective
Verify dashboard menampilkan data yang akurat

### Check Dashboard Cards
1. Login & go to Dashboard
2. Check statistic cards:
   - Total Items
   - Total Suppliers
   - Total Locations
   - Total Stock Value

**Expected Result:**
- ✅ Total Items: 5 (dari seeding) + 1 (test) = 6 atau lebih
- ✅ Total Suppliers: 3 + 1 (test) = 4 atau lebih
- ✅ Total Locations: 6 + 1 (test) = 7 atau lebih
- ✅ Total Stock Value: Rp XX.XXX.XXX (total qty × unit price)
- ✅ Pending PO: menampilkan count PO dengan status pending
- ✅ Pending SO: menampilkan count SO dengan status pending

### Check Low Stock Alert
1. Check "Items with Low Stock" section

**Expected Result:**
- ✅ Menampilkan items dengan total stock <= minimum_stock
- ✅ Menampilkan item, stock, min stock, status

---

## 🔧 Automated Testing (Optional)

### Unit Tests untuk Models
```bash
php artisan test tests/Unit/ItemTest.php
php artisan test tests/Unit/StockTest.php
php artisan test tests/Unit/SalesOrderTest.php
```

### Feature Tests untuk Controllers
```bash
php artisan test tests/Feature/SupplierControllerTest.php
php artisan test tests/Feature/PurchaseOrderControllerTest.php
php artisan test tests/Feature/SalesOrderControllerTest.php
```

### Run All Tests
```bash
php artisan test
```

---

## 📝 Test Report Template

```
Test Date: [Date]
Tester: [Name]
Environment: Development
Browser: [Chrome/Firefox/Safari]

Test Results:
[ ] Test Case 1 - Authentication ✅ PASS / ❌ FAIL
[ ] Test Case 2 - Supplier CRUD ✅ PASS / ❌ FAIL
[ ] Test Case 3 - Item CRUD ✅ PASS / ❌ FAIL
[ ] Test Case 4 - Location CRUD ✅ PASS / ❌ FAIL
[ ] Test Case 5 - User Management ✅ PASS / ❌ FAIL
[ ] Test Case 6 - PO Workflow ✅ PASS / ❌ FAIL
[ ] Test Case 7 - SO Workflow ✅ PASS / ❌ FAIL
[ ] Test Case 8 - Stock Validation ✅ PASS / ❌ FAIL
[ ] Test Case 9 - Stock Movements ✅ PASS / ❌ FAIL
[ ] Test Case 10 - FIFO Logic ✅ PASS / ❌ FAIL
[ ] Test Case 11 - Authorization ✅ PASS / ❌ FAIL
[ ] Test Case 12 - Dashboard ✅ PASS / ❌ FAIL

Issues Found:
[List any issues or bugs]

Overall Result: ✅ PASS / ❌ FAIL WITH ISSUES
```

---

## 🐛 Common Issues & Solutions During Testing

### Issue: "SQLSTATE[HY000]: General error"
**Solution:** Run migration dan seeding ulang
```bash
php artisan migrate:refresh --seed
```

### Issue: "Call to undefined method"
**Solution:** Clear Laravel cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Issue: Assets not loading (CSS/JS)
**Solution:** Build assets
```bash
npm run build
```

### Issue: 419 CSRF token mismatch
**Solution:** Ensure form includes @csrf token
```blade
@csrf
```

### Issue: 403 Unauthorized when accessing admin routes
**Solution:** 
1. Verify you're logged in as admin
2. Check user.role = 'admin' in database
```bash
php artisan tinker
> User::find(1)->role
=> "admin"
```

---

**Last Updated**: January 3, 2026

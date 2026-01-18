API & Endpoints Documentation

Dokumen ini berisi daftar endpoint utama yang digunakan pada Inventory & Warehouse Management System berbasis Laravel.

Authentication (Laravel Breeze)
Method	Endpoint	Keterangan
GET	/login	Halaman login
POST	/login	Proses login
POST	/logout	Logout
GET	/register	Halaman registrasi
POST	/register	Proses registrasi
GET	/forgot-password	Lupa password
POST	/forgot-password	Kirim reset password
GET	/reset-password/{token}	Form reset password
POST	/reset-password	Proses reset password
Dashboard
Method	Endpoint	Akses	Keterangan
GET	/dashboard	Admin, User	Dashboard utama
Master Data
Supplier
Method	Endpoint	Akses	Keterangan
GET	/suppliers	Admin, User	List supplier
GET	/suppliers/create	Admin, User	Form tambah
POST	/suppliers	Admin, User	Simpan supplier
GET	/suppliers/{id}	Admin, User	Detail supplier
GET	/suppliers/{id}/edit	Admin, User	Form edit
PUT	/suppliers/{id}	Admin, User	Update
DELETE	/suppliers/{id}	Admin, User	Hapus (jika tidak ada PO aktif)
Location
Method	Endpoint	Keterangan
GET	/locations	List lokasi
GET	/locations/create	Form tambah
POST	/locations	Simpan lokasi
GET	/locations/{id}	Detail lokasi
GET	/locations/{id}/edit	Form edit
PUT	/locations/{id}	Update
DELETE	/locations/{id}	Hapus
Item
Method	Endpoint	Keterangan
GET	/items	List item
GET	/items/create	Form tambah
POST	/items	Simpan item
GET	/items/{id}	Detail item & stok
GET	/items/{id}/edit	Form edit
PUT	/items/{id}	Update
DELETE	/items/{id}	Hapus
User (Admin Only)
Method	Endpoint	Keterangan
GET	/users	List user
GET	/users/create	Form tambah
POST	/users	Simpan user
GET	/users/{id}	Detail user
GET	/users/{id}/edit	Form edit
PUT	/users/{id}	Update
DELETE	/users/{id}	Hapus
Transaction
Purchase Order (Admin Only)
Method	Endpoint	Keterangan
GET	/purchase_orders	List PO
GET	/purchase_orders/create	Form PO
POST	/purchase_orders	Buat PO (draft)
GET	/purchase_orders/{id}	Detail PO
POST	/purchase_orders/{id}/confirm	Draft → Pending
POST	/purchase_orders/{id}/receive	Terima barang & update stok
POST	/purchase_orders/{id}/cancel	Batalkan PO
Sales Order
Method	Endpoint	Akses	Keterangan
GET	/sales_orders	Admin, User	List SO
GET	/sales_orders/create	Admin, User	Form SO
POST	/sales_orders	Admin, User	Buat SO
GET	/sales_orders/{id}	Admin, User	Detail SO
POST	/sales_orders/{id}/confirm	Admin, User	Draft → Pending
POST	/sales_orders/{id}/ship	Admin	Ship & kurangi stok
POST	/sales_orders/{id}/cancel	Admin, User	Batalkan SO
Authorization & Middleware

auth → User harus login

admin → Khusus role Admin

User hanya bisa mengakses Sales Order miliknya sendiri

Proses shipping hanya dapat dilakukan oleh Admin

Notes

Sistem menggunakan validasi stok otomatis sebelum pengiriman

Metode pengambilan stok menggunakan FIFO

Semua pergerakan stok tercatat pada tabel stock_movements
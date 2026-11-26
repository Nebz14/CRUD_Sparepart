# 🔧 BengkelShop — PHP Native MVC

Toko sparepart & aksesoris dengan arsitektur PHP Native MVC, Bootstrap 5.3,
dan PDO prepared statement.

## Struktur

```
config/database.php        # koneksi PDO -> $pdo
core/helpers.php           # session, flash, redirect, proteksi role
controllers/
  AuthController.php       # login, register, logout
  UserController.php       # katalog, keranjang, checkout, riwayat
  AdminController.php      # kerangka (diisi tim Admin)
  OwnerController.php      # kerangka (diisi tim Owner)
models/
  UserModel.php
  ProductModel.php
  TransactionModel.php
views/
  layouts/header.php       # head, navbar dinamis, alert flash
  layouts/footer.php
  auth/login.php
  auth/register.php
  user/katalog.php
  user/keranjang.php
  user/riwayat.php
  user/detail_transaksi.php
  errors/404.php
index.php                  # front controller / router ?page=...
update_db.sql              # skema tabel + akun demo
img/                       # gambar produk
```

## Setup

1. Pastikan Apache & MySQL (WAMP) berjalan.
2. Import skema: buka phpMyAdmin lalu jalankan `update_db.sql`,
   atau lewat CLI: `mysql -u root db_sparepart < update_db.sql`.
3. Akses `http://localhost/tts_pw/`.

Akun demo (password semua: `password123`):

| Username | Role  |
|----------|-------|
| admin    | admin |
| user1    | user  |
| owner    | owner |

## Kontrak Session

- `$_SESSION['user_id']`
- `$_SESSION['nama']`
- `$_SESSION['role']` → `admin` | `user` | `owner`
- `$_SESSION['cart']` → `[id_produk => jumlah]`
- `$_SESSION['flash']` → `['type' => 'success'|'danger', 'message' => '...']`

## Route `index.php?page=...`

**Auth & User**

`login`, `register`, `logout`, `user-katalog`, `user-keranjang`,
`user-cart-add`, `user-cart-update`, `user-cart-delete`, `user-checkout`,
`user-riwayat`, `user-transaksi-detail`

**Admin (kerangka untuk rekan tim)**

`admin-dashboard`, `admin-produk`, `admin-produk-tambah`, `admin-produk-edit`,
`admin-produk-hapus`, `admin-transaksi`, `admin-transaksi-detail`,
`admin-transaksi-status`

**Owner (kerangka untuk rekan tim)**

`owner-dashboard`

## Catatan Checkout

`user-checkout` memakai `beginTransaction()` / `commit()`:

1. Validasi & kunci baris produk (`SELECT ... FOR UPDATE`).
2. Insert `tb_transaksi` (status awal `Menunggu Konfirmasi`).
3. Insert `tb_detail_transaksi` per item.
4. Kurangi `stok` di `tb_produk`.
5. Rollback otomatis bila ada yang gagal, lalu keranjang dikosongkan.

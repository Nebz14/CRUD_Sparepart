-- ============================================================
--  BengkelShop - update database
--  Jalankan file ini sekali pada database db_sparepart.
--  Aman dijalankan berulang (CREATE TABLE IF NOT EXISTS,
--  dan akun demo memakai ON DUPLICATE KEY UPDATE).
-- ============================================================

CREATE DATABASE IF NOT EXISTS db_sparepart
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE db_sparepart;

-- Tabel produk lama (disertakan agar setup database baru tetap lengkap).
CREATE TABLE IF NOT EXISTS tb_produk (
    id_produk   INT NOT NULL AUTO_INCREMENT,
    kode_sku    VARCHAR(20) NOT NULL,
    nama_barang VARCHAR(100) NOT NULL,
    kategori    VARCHAR(50) NOT NULL,
    harga       INT NOT NULL,
    stok        INT NOT NULL DEFAULT 0,
    gambar_url  VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_produk),
    UNIQUE KEY uq_produk_sku (kode_sku)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel tb_produk lama bisa saja masih MyISAM. Ubah ke InnoDB agar
-- foreign key dan transaksi pengurangan stok berjalan dengan benar.
ALTER TABLE tb_produk ENGINE=InnoDB;

-- Akun pengguna.
CREATE TABLE IF NOT EXISTS tb_users (
    id_user    INT NOT NULL AUTO_INCREMENT,
    nama       VARCHAR(100) NOT NULL,
    username   VARCHAR(50) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('admin', 'user', 'owner') NOT NULL DEFAULT 'user',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_user),
    UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Header transaksi / pesanan.
CREATE TABLE IF NOT EXISTS tb_transaksi (
    id_transaksi   INT NOT NULL AUTO_INCREMENT,
    kode_transaksi VARCHAR(30) NOT NULL,
    id_user        INT NOT NULL,
    total_harga    INT NOT NULL DEFAULT 0,
    alamat         TEXT NOT NULL,
    status         ENUM('Menunggu Konfirmasi', 'Diproses', 'Selesai', 'Dibatalkan')
                   NOT NULL DEFAULT 'Menunggu Konfirmasi',
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_transaksi),
    UNIQUE KEY uq_transaksi_kode (kode_transaksi),
    KEY idx_transaksi_user (id_user),
    KEY idx_transaksi_status (status),
    CONSTRAINT fk_transaksi_user
        FOREIGN KEY (id_user) REFERENCES tb_users (id_user)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Detail item transaksi.
CREATE TABLE IF NOT EXISTS tb_detail_transaksi (
    id_detail    INT NOT NULL AUTO_INCREMENT,
    id_transaksi INT NOT NULL,
    id_produk    INT NOT NULL,
    jumlah       INT NOT NULL,
    subtotal     INT NOT NULL,
    PRIMARY KEY (id_detail),
    KEY idx_detail_transaksi (id_transaksi),
    KEY idx_detail_produk (id_produk),
    CONSTRAINT fk_detail_transaksi
        FOREIGN KEY (id_transaksi) REFERENCES tb_transaksi (id_transaksi)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_detail_produk
        FOREIGN KEY (id_produk) REFERENCES tb_produk (id_produk)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Akun demo. Semua password: password123
-- Hash di bawah dibuat dengan password_hash('password123', PASSWORD_DEFAULT).
INSERT INTO tb_users (nama, username, password, role) VALUES
    ('Administrator', 'admin',  '$2y$12$UhD1m3MWmQ28339GbXwpku1S8oZ5skldb/ObLW96Nt9kvBQTBlQye', 'admin'),
    ('User Demo',     'user1',  '$2y$12$UhD1m3MWmQ28339GbXwpku1S8oZ5skldb/ObLW96Nt9kvBQTBlQye', 'user'),
    ('Owner Bengkel', 'owner',  '$2y$12$UhD1m3MWmQ28339GbXwpku1S8oZ5skldb/ObLW96Nt9kvBQTBlQye', 'owner')
ON DUPLICATE KEY UPDATE
    nama     = VALUES(nama),
    password = VALUES(password),
    role     = VALUES(role);

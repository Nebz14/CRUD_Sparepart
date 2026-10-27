<?php
require_once 'connection.php';

$error_msg = "";

if (isset($_POST['submit'])) {
    $kode_sku    = $_POST['kode_sku'];
    $nama_barang = $_POST['nama_barang'];
    $kategori    = $_POST['kategori'];
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];

    // Upload Gambar
    $nama_file   = $_FILES['gambar']['name'];
    $tmp_file    = $_FILES['gambar']['tmp_name'];
    $path_simpan = "";

    if (!empty($nama_file)) {
        $nama_file_baru = time() . '-' . basename($nama_file);
        $target_dir     = "img/" . $nama_file_baru;

        if (move_uploaded_file($tmp_file, $target_dir)) {
            $path_simpan = $target_dir;
        }
    }

    // Query SQL
    $sql = "INSERT INTO tb_produk (kode_sku, nama_barang, kategori, harga, stok, gambar_url) 
            VALUES (:kode_sku, :nama_barang, :kategori, :harga, :stok, :gambar_url)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':kode_sku'    => $kode_sku,
            ':nama_barang' => $nama_barang,
            ':kategori'    => $kategori,
            ':harga'       => $harga,
            ':stok'        => $stok,
            ':gambar_url'  => $path_simpan
        ]);
        
        // Hapus alert dulu sementara untuk testing
        echo "BERHASIL SIMPAN! <a href='index.php'>Lihat Data</a>";
        exit;
        
    } catch (PDOException $e) {
        // Tampilkan error database langsung di layar
        die("Error Database: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Toko Sparepart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <!-- Alert jika ada error -->
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> <?= $error_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tambah Produk Baru</h5>
                </div>
                <div class="card-body">
                    <form action="create.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Kode SKU</label>
                            <input type="text" name="kode_sku" class="form-control" placeholder="Contoh: BAN-001" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Sparepart">Sparepart</option>
                                <option value="Aksesoris">Aksesoris</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga (Rp)</label>
                                <input type="number" name="harga" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok Awal</label>
                                <input type="number" name="stok" class="form-control" min="0" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">File Gambar Produk</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">Kembali</a>
                            <!-- Pastikan type="submit" dan name="submit" ada -->
                            <button type="submit" name="submit" class="btn btn-success">Simpan Produk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<!-- anjay -->
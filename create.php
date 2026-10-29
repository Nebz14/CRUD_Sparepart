<?php
require_once 'connection.php';

$error_msg = "";

if (isset($_POST['submit'])) {
    $kode_sku    = trim($_POST['kode_sku']);
    $nama_barang = trim($_POST['nama_barang']);
    $kategori    = $_POST['kategori'];
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];

    $path_simpan = "";
    $target_dir = "img/";
    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $nama_file   = $_FILES['gambar']['name'];
        $tmp_file    = $_FILES['gambar']['tmp_name'];
        
        $ext = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed_ext)) {
            $nama_file_baru = time() . '-' . uniqid() . '.' . $ext;
            $path_simpan    = $target_dir . $nama_file_baru;

            move_uploaded_file($tmp_file, $path_simpan);
        }
    }

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
        
        if ($result) {
            echo "<script>
                    alert('Berhasil menyimpan data!');
                    window.location.href='index.php';
                  </script>";
            exit;
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error_msg = "Kode SKU '$kode_sku' sudah digunakan. Gunakan kode SKU lain!";
        } else {
            $error_msg = "Error Database: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - BengkelShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bg-navy { background-color: #202940; }
        .btn-custom { background-color: #9A8678; color: white; border: none; }
        .btn-custom:hover { background-color: #4B4038; color: white; }
    </style>
</head>
<body style="background-color: #f8f9fa;">

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error_msg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-navy text-white py-3">
                    <h5 class="mb-0">Tambah Produk Baru</h5>
                </div>
                <div class="card-body p-4">
                    <form action="create.php" method="POST" enctype="multipart/form-data">
                        <!-- Sisanya sama persis dengan aslinya (input form) -->
                        <div class="mb-3">
                            <label class="form-label text-muted">Kode SKU</label>
                            <input type="text" name="kode_sku" class="form-control" placeholder="Contoh: BAN-001" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Sparepart">Sparepart</option>
                                <option value="Aksesoris">Aksesoris</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Harga (Rp)</label>
                                <input type="number" name="harga" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Stok Awal</label>
                                <input type="number" name="stok" class="form-control" min="0" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted">File Gambar Produk</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                        </div>
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="index.php" class="btn btn-outline-secondary">Kembali</a>
                            <button type="submit" name="submit" class="btn btn-custom px-4">Simpan Produk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
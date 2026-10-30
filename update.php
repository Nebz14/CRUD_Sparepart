<?php
require_once 'connection.php';

$error_msg = "";
$id = $_GET['id'] ?? null;

// Jika tidak ada ID di URL, kembalikan ke index
if (!$id) {
    header("Location: index.php");
    exit;
}

// 1. Ambil data produk saat ini
$stmt = $pdo->prepare("SELECT * FROM tb_produk WHERE id_produk = :id");
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produk) {
    die("Data produk tidak ditemukan!");
}

// 2. Proses jika tombol update ditekan
if (isset($_POST['update'])) {
    $kode_sku    = trim($_POST['kode_sku']);
    $nama_barang = trim($_POST['nama_barang']);
    $kategori    = $_POST['kategori'];
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];
    
    $path_simpan = $produk['gambar_url']; // Default: gunakan lokasi gambar lama

    // Cek jika ada upload gambar baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "img/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $nama_file = $_FILES['gambar']['name'];
        $tmp_file  = $_FILES['gambar']['tmp_name'];
        $ext       = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed_ext)) {
            // Hapus file gambar fisik yang lama jika ada agar tidak menumpuk di folder
            if (!empty($produk['gambar_url']) && file_exists($produk['gambar_url'])) {
                unlink($produk['gambar_url']);
            }

            $nama_file_baru = time() . '-' . uniqid() . '.' . $ext;
            $path_simpan    = $target_dir . $nama_file_baru;
            move_uploaded_file($tmp_file, $path_simpan);
        }
    }

    // 3. Query UPDATE data
    $sql = "UPDATE tb_produk 
            SET kode_sku = :kode_sku, 
                nama_barang = :nama_barang, 
                kategori = :kategori, 
                harga = :harga, 
                stok = :stok, 
                gambar_url = :gambar_url 
            WHERE id_produk = :id";
    
    try {
        $update_stmt = $pdo->prepare($sql);
        $result = $update_stmt->execute([
            ':kode_sku'    => $kode_sku,
            ':nama_barang' => $nama_barang,
            ':kategori'    => $kategori,
            ':harga'       => $harga,
            ':stok'        => $stok,
            ':gambar_url'  => $path_simpan,
            ':id'          => $id
        ]);
        
        if ($result) {
            echo "<script>
                    alert('Berhasil memperbarui data produk!');
                    window.location.href='index.php';
                  </script>";
            exit;
        }
    } catch (PDOException $e) {
        // Menangkap error duplikasi SKU
        if ($e->getCode() == 23000) {
            $error_msg = "Kode SKU '$kode_sku' sudah digunakan oleh produk lain.";
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
    <title>Edit Produk - BengkelShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bg-beige { background-color: #CAAA98; color: #4B4038; }
        .btn-custom { background-color: #4B4038; color: white; border: none; }
        .btn-custom:hover { background-color: #202940; color: white; }
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
                <div class="card-header bg-beige py-3">
                    <h5 class="mb-0 fw-bold">Edit Data Produk</h5>
                </div>
                <div class="card-body p-4">
                    <form action="update.php?id=<?= $id; ?>" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label text-muted">Kode SKU</label>
                            <input type="text" name="kode_sku" class="form-control" value="<?= htmlspecialchars($produk['kode_sku']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($produk['nama_barang']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="Sparepart" <?= $produk['kategori'] == 'Sparepart' ? 'selected' : ''; ?>>Sparepart</option>
                                <option value="Aksesoris" <?= $produk['kategori'] == 'Aksesoris' ? 'selected' : ''; ?>>Aksesoris</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Harga (Rp)</label>
                                <input type="number" name="harga" class="form-control" min="0" value="<?= $produk['harga']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Stok Saat Ini</label>
                                <input type="number" name="stok" class="form-control" min="0" value="<?= $produk['stok']; ?>" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label d-block text-muted">Gambar Produk</label>
                            <?php if (!empty($produk['gambar_url'])): ?>
                                <img src="<?= htmlspecialchars($produk['gambar_url']); ?>" alt="Gambar Saat Ini" height="100" class="mb-3 rounded border object-fit-cover">
                            <?php endif; ?>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                        </div>
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="index.php" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" name="update" class="btn btn-custom px-4">Update Produk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php
require_once 'connection.php';

try {
    $stmt = $pdo->query("SELECT * FROM tb_produk ORDER BY id_produk DESC");
    $produk_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - Toko Sparepart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --dark-navy: #202940;
            --dark-brown: #4B4038;
            --light-brown: #9A8678;
            --beige: #CAAA98;
        }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-custom { background-color: var(--dark-navy); }
        .text-navy { color: var(--dark-navy); }
        .text-price { color: var(--dark-brown); font-weight: bold; font-size: 1.1rem; }
        .btn-custom-primary { background-color: var(--light-brown); color: white; border: none; }
        .btn-custom-primary:hover { background-color: var(--dark-brown); color: white; }
        .btn-custom-outline { border: 1px solid var(--light-brown); color: var(--dark-brown); }
        .btn-custom-outline:hover { background-color: var(--beige); color: var(--dark-brown); }
        .product-card { transition: transform 0.2s, box-shadow 0.2s; border: none; border-radius: 12px; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(32, 41, 64, 0.15) !important; }
        .card-img-wrapper { height: 200px; overflow: hidden; border-radius: 12px 12px 0 0; background-color: #eee; }
        .card-img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .badge-category { background-color: var(--beige); color: var(--dark-brown); font-weight: 500; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🔧 BengkelShop</a>
    </div>
</nav>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-navy mb-0">Daftar Produk</h4>
            <small class="text-muted">Kelola katalog dan stok barang</small>
        </div>
        <a href="create.php" class="btn btn-custom-primary shadow-sm">+ Tambah Produk</a>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php if (count($produk_list) > 0): ?>
            <?php foreach ($produk_list as $row): ?>
                <div class="col">
                    <div class="card h-100 product-card shadow-sm">
                        <div class="card-img-wrapper">
                            <?php if (!empty($row['gambar_url'])): ?>
                                <img src="<?= htmlspecialchars($row['gambar_url']); ?>" alt="<?= htmlspecialchars($row['nama_barang']); ?>">
                            <?php else: ?>
                                <div class="d-flex h-100 justify-content-center align-items-center text-muted">No Image</div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge badge-category"><?= htmlspecialchars($row['kategori']); ?></span>
                                <small class="text-muted" style="font-size: 0.75rem;">SKU: <?= htmlspecialchars($row['kode_sku']); ?></small>
                            </div>
                            <h6 class="card-title text-truncate mb-1" title="<?= htmlspecialchars($row['nama_barang']); ?>">
                                <?= htmlspecialchars($row['nama_barang']); ?>
                            </h6>
                            <p class="text-price mb-2">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
                            
                            <div class="mt-auto">
                                <p class="mb-3" style="font-size: 0.85rem;">
                                    Sisa Stok: 
                                    <?php if ($row['stok'] < 5): ?>
                                        <span class="text-danger fw-bold"><?= $row['stok']; ?> (Segera Restock)</span>
                                    <?php else: ?>
                                        <span class="text-success fw-bold"><?= $row['stok']; ?> pcs</span>
                                    <?php endif; ?>
                                </p>
                                <div class="d-flex gap-2">
                                    <a href="update.php?id=<?= $row['id_produk']; ?>" class="btn btn-sm btn-custom-outline flex-grow-1">Edit</a>
                                    <a href="delete.php?id=<?= $row['id_produk']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus produk ini?');">Hapus</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Belum ada data produk.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
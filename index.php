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
    <title>Daftar Produk - Toko Sparepart</title>
    <!-- CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary">Toko Sparepart & Aksesoris Motor</h2>
            <p class="text-muted mb-0">Kelola katalog dan stok barang dengan mudah</p>
        </div>
        <a href="create.php" class="btn btn-primary">+ Tambah Produk</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">No</th>
                            <th>Gambar</th>
                            <th>SKU</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($produk_list) > 0): ?>
                            <?php $no = 1; foreach ($produk_list as $row): ?>
                                <tr>
                                    <td class="ps-3"><?= $no++; ?></td>
                                    <td>
                                        <?php if (!empty($row['gambar_url'])): ?>
                                            <img src="<?= htmlspecialchars($row['gambar_url']); ?>" alt="Gambar" width="50" height="50" class="rounded object-fit-cover">
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['kode_sku']); ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark"><?= htmlspecialchars($row['kategori']); ?></span>
                                    </td>
                                    <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php if ($row['stok'] < 5): ?>
                                            <span class="badge bg-danger">Restock (<?= $row['stok']; ?>)</span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?= $row['stok']; ?> pcs</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="edit.php?id=<?= $row['id_produk']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="delete.php?id=<?= $row['id_produk']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada data produk. Silakan tambah data baru.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
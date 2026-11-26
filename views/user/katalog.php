<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-navy mb-1">Katalog Sparepart</h4>
        <p class="text-muted mb-0">Temukan sparepart dan aksesoris untuk kendaraan Anda.</p>
    </div>
    <?php if (current_role() === 'user'): ?>
        <a href="index.php?page=user-keranjang" class="btn btn-custom-primary shadow-sm">
            🛒 Keranjang
            <span class="badge text-bg-light ms-1"><?= cart_items_count() ?></span>
        </a>
    <?php endif; ?>
</div>

<form method="GET" action="index.php" class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <input type="hidden" name="page" value="user-katalog">
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label for="q" class="form-label text-muted small mb-1">Cari produk</label>
                <input type="text" class="form-control" id="q" name="q"
                       value="<?= e($search) ?>" placeholder="Nama barang atau kode SKU...">
            </div>
            <div class="col-md-4">
                <label for="kategori" class="form-label text-muted small mb-1">Kategori</label>
                <select class="form-select" id="kategori" name="kategori">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategoriList as $itemKategori): ?>
                        <option value="<?= e($itemKategori) ?>" <?= $kategori === $itemKategori ? 'selected' : '' ?>>
                            <?= e($itemKategori) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-custom-primary flex-grow-1">Cari</button>
                <a href="index.php?page=user-katalog" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </div>
</form>

<div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php if (count($produkList) > 0): ?>
        <?php foreach ($produkList as $row): ?>
            <div class="col">
                <div class="card h-100 product-card shadow-sm">
                    <div class="card-img-wrapper">
                        <?php if (!empty($row['gambar_url'])): ?>
                            <img src="<?= e($row['gambar_url']) ?>" alt="<?= e($row['nama_barang']) ?>">
                        <?php else: ?>
                            <div class="d-flex h-100 justify-content-center align-items-center text-muted">No Image</div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge badge-category"><?= e($row['kategori']) ?></span>
                            <small class="text-muted" style="font-size: 0.75rem;">SKU: <?= e($row['kode_sku']) ?></small>
                        </div>
                        <h6 class="card-title text-truncate mb-1" title="<?= e($row['nama_barang']) ?>">
                            <?= e($row['nama_barang']) ?>
                        </h6>
                        <p class="text-price mb-2"><?= format_rupiah($row['harga']) ?></p>

                        <div class="mt-auto">
                            <p class="mb-3" style="font-size: 0.85rem;">
                                Sisa Stok:
                                <?php if ((int) $row['stok'] < 5): ?>
                                    <span class="text-danger fw-bold"><?= (int) $row['stok'] ?> (Segera Restock)</span>
                                <?php else: ?>
                                    <span class="text-success fw-bold"><?= (int) $row['stok'] ?> pcs</span>
                                <?php endif; ?>
                            </p>

                            <?php if (current_role() === 'user'): ?>
                                <form action="index.php?page=user-cart-add" method="POST">
                                    <input type="hidden" name="id_produk" value="<?= (int) $row['id_produk'] ?>">
                                    <input type="hidden" name="jumlah" value="1">
                                    <input type="hidden" name="back" value="<?= e($backUrl) ?>">
                                    <button type="submit" class="btn btn-sm btn-custom-primary w-100"
                                        <?= (int) $row['stok'] < 1 ? 'disabled' : '' ?>>
                                        <?= (int) $row['stok'] < 1 ? 'Stok Habis' : '+ Keranjang' ?>
                                    </button>
                                </form>
                            <?php elseif (current_role() === null): ?>
                                <a href="index.php?page=login" class="btn btn-sm btn-custom-outline w-100">
                                    Masuk untuk membeli
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <p class="text-muted mb-1">Produk tidak ditemukan.</p>
            <a href="index.php?page=user-katalog" class="text-decoration-none">&larr; Lihat semua produk</a>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

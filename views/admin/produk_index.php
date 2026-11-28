<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div><h4 class="fw-bold text-navy mb-1">Kelola Produk</h4><p class="text-muted mb-0">Tambah, ubah, dan hapus data sparepart serta aksesoris.</p></div>
    <a href="index.php?page=admin-produk-tambah" class="btn btn-custom-primary">+ Tambah Produk</a>
</div>

<form method="GET" action="index.php" class="card border-0 shadow-sm mb-3">
    <div class="card-body"><input type="hidden" name="page" value="admin-produk">
        <div class="row g-2">
            <div class="col-md-7"><input class="form-control" type="search" name="q" value="<?= e($search) ?>" placeholder="Cari nama barang atau SKU"></div>
            <div class="col-md-3"><select class="form-select" name="kategori">
                <option value="">Semua kategori</option>
                <option value="Sparepart" <?= $kategori === 'Sparepart' ? 'selected' : '' ?>>Sparepart</option>
                <option value="Aksesoris" <?= $kategori === 'Aksesoris' ? 'selected' : '' ?>>Aksesoris</option>
            </select></div>
            <div class="col-md-2 d-flex gap-2"><button class="btn btn-custom-primary flex-grow-1">Filter</button><a href="index.php?page=admin-produk" class="btn btn-outline-secondary">Reset</a></div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm"><div class="table-responsive">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr><th>Produk</th><th>SKU</th><th>Kategori</th><th>Harga</th><th>Stok</th><th class="text-end">Aksi</th></tr></thead>
    <tbody>
    <?php if (!$produkList): ?><tr><td colspan="6" class="text-center text-muted py-4">Belum ada data produk.</td></tr>
    <?php else: foreach ($produkList as $p): ?>
        <tr>
            <td><div class="d-flex align-items-center gap-2">
                <?php if (!empty($p['gambar_url'])): ?><img src="<?= e($p['gambar_url']) ?>" class="table-thumb" alt="<?= e($p['nama_barang']) ?>"><?php else: ?><div class="table-thumb d-flex align-items-center justify-content-center text-muted small">No img</div><?php endif; ?>
                <span class="fw-semibold"><?= e($p['nama_barang']) ?></span>
            </div></td>
            <td><?= e($p['kode_sku']) ?></td><td><span class="badge badge-category"><?= e($p['kategori']) ?></span></td>
            <td><?= format_rupiah($p['harga']) ?></td>
            <td><?php if ((int)$p['stok'] < 5): ?><span class="badge text-bg-danger"><?= (int)$p['stok'] ?></span><?php else: ?><span class="badge text-bg-success"><?= (int)$p['stok'] ?></span><?php endif; ?></td>
            <td class="text-end text-nowrap">
                <a class="btn btn-sm btn-custom-outline" href="index.php?page=admin-produk-edit&id=<?= (int)$p['id_produk'] ?>">Edit</a>
                <a class="btn btn-sm btn-outline-danger" href="index.php?page=admin-produk-hapus&id=<?= (int)$p['id_produk'] ?>" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
            </td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table></div></div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

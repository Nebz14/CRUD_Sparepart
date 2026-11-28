<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h4 class="fw-bold text-navy mb-1">Dashboard Admin</h4>
        <p class="text-muted mb-0">Ringkasan aktivitas toko BengkelShop.</p>
    </div>
    <a href="index.php?page=admin-produk-tambah" class="btn btn-custom-primary">+ Tambah Produk</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body">
        <div class="text-muted small">Total Produk</div><div class="fs-3 fw-bold text-navy"><?= (int)$jumlahProduk ?></div>
    </div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body">
        <div class="text-muted small">Total Transaksi</div><div class="fs-3 fw-bold text-navy"><?= (int)$ringkasan['total_transaksi'] ?></div>
    </div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body">
        <div class="text-muted small">Menunggu Konfirmasi</div><div class="fs-3 fw-bold text-warning"><?= (int)$ringkasan['menunggu'] ?></div>
    </div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body">
        <div class="text-muted small">Omzet Transaksi Selesai</div><div class="fs-4 fw-bold text-navy"><?= format_rupiah($ringkasan['omzet']) ?></div>
    </div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold text-navy py-3">Status Transaksi</div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2 border-bottom"><span>Menunggu Konfirmasi</span><strong><?= (int)$ringkasan['menunggu'] ?></strong></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span>Diproses</span><strong><?= (int)$ringkasan['diproses'] ?></strong></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span>Selesai</span><strong><?= (int)$ringkasan['selesai'] ?></strong></div>
                <div class="d-flex justify-content-between py-2"><span>Dibatalkan</span><strong><?= (int)$ringkasan['dibatalkan'] ?></strong></div>
                <a href="index.php?page=admin-transaksi" class="btn btn-custom-outline btn-sm mt-3">Kelola Transaksi</a>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold text-navy py-3">Stok Menipis (&lt; 5)</div>
            <div class="card-body">
                <?php if (!$stokMenipis): ?><p class="text-muted mb-0">Tidak ada produk dengan stok menipis.</p>
                <?php else: foreach ($stokMenipis as $p): ?>
                    <div class="d-flex justify-content-between gap-2 py-2 border-bottom">
                        <span><?= e($p['nama_barang']) ?><small class="d-block text-muted"><?= e($p['kode_sku']) ?></small></span>
                        <span class="badge text-bg-danger align-self-center"><?= (int)$p['stok'] ?> pcs</span>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

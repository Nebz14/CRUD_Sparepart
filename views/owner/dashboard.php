<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-4"><h4 class="fw-bold text-navy mb-1">Dashboard Owner</h4><p class="text-muted mb-0">Laporan penjualan dan pemantauan persediaan BengkelShop.</p></div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="small text-muted">Omzet Penjualan Selesai</div><div class="fs-4 fw-bold text-navy"><?= format_rupiah($ringkasan['omzet']) ?></div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="small text-muted">Total Transaksi</div><div class="fs-3 fw-bold text-navy"><?= (int)$ringkasan['total_transaksi'] ?></div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="small text-muted">Transaksi Selesai</div><div class="fs-3 fw-bold text-success"><?= (int)$ringkasan['selesai'] ?></div></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="small text-muted">Perlu Konfirmasi</div><div class="fs-3 fw-bold text-warning"><?= (int)$ringkasan['menunggu'] ?></div></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-7"><div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white py-3 fw-bold text-navy">Transaksi Terbaru</div>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Kode</th><th>Pelanggan</th><th>Total</th><th>Status</th></tr></thead>
            <tbody><?php if (!$transaksiList): ?><tr><td colspan="4" class="text-center text-muted py-3">Belum ada transaksi.</td></tr>
            <?php else: foreach ($transaksiList as $trx): ?><tr>
                <td><?= e($trx['kode_transaksi']) ?></td><td><?= e($trx['nama']) ?></td><td><?= format_rupiah($trx['total_harga']) ?></td>
                <td><span class="badge text-bg-<?= e(status_badge_class($trx['status'])) ?>"><?= e($trx['status']) ?></span></td>
            </tr><?php endforeach; endif; ?></tbody>
        </table></div>
    </div></div>
    <div class="col-lg-5"><div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white py-3 fw-bold text-navy">Daftar Stok Menipis (&lt; 5)</div>
        <div class="card-body">
            <?php if (!$stokMenipis): ?><p class="text-muted mb-0">Semua stok produk berada pada kondisi aman.</p>
            <?php else: foreach ($stokMenipis as $p): ?><div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div><div class="fw-semibold"><?= e($p['nama_barang']) ?></div><small class="text-muted"><?= e($p['kode_sku']) ?> · <?= e($p['kategori']) ?></small></div>
                <span class="badge text-bg-danger"><?= (int)$p['stok'] ?> pcs</span>
            </div><?php endforeach; endif; ?>
        </div>
    </div></div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

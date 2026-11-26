<?php include 'views/layouts/header.php'; ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div><h4 class="fw-bold text-navy mb-1">Detail Transaksi</h4><p class="text-muted mb-0"><?= e($transaksi['kode_transaksi']) ?></p></div>
    <a href="index.php?page=admin-transaksi" class="btn btn-custom-outline">&larr; Kembali ke Transaksi</a>
</div>

<div class="row g-4">
    <div class="col-lg-8"><div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 fw-bold text-navy">Item Pesanan</div>
        <div class="table-responsive"><table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>Produk</th><th>Harga Satuan</th><th>Jumlah</th><th class="text-end">Subtotal</th></tr></thead>
            <tbody><?php if (!$items): ?><tr><td colspan="4" class="text-center text-muted py-4">Tidak ada detail item.</td></tr>
            <?php else: foreach ($items as $item): ?><tr>
                <td><span class="fw-semibold"><?= e($item['nama_barang'] ?? '(Produk telah dihapus)') ?></span><small class="d-block text-muted">SKU: <?= e($item['kode_sku'] ?? '-') ?></small></td>
                <td><?= format_rupiah($item['harga'] ?? 0) ?></td><td><?= (int)$item['jumlah'] ?> pcs</td><td class="text-end fw-semibold"><?= format_rupiah($item['subtotal']) ?></td>
            </tr><?php endforeach; endif; ?></tbody>
            <tfoot><tr><th colspan="3" class="text-end">Total Transaksi</th><th class="text-end text-price"><?= format_rupiah($transaksi['total_harga']) ?></th></tr></tfoot>
        </table></div>
    </div></div>
    <div class="col-lg-4"><div class="card border-0 shadow-sm">
        <div class="card-header bg-navy text-white py-3 fw-semibold">Informasi Pelanggan</div>
        <div class="card-body">
            <div class="small text-muted">Nama pelanggan</div><div class="fw-semibold mb-3"><?= e($transaksi['nama']) ?> (<?= e($transaksi['username']) ?>)</div>
            <div class="small text-muted">Tanggal pesanan</div><div class="fw-semibold mb-3"><?= e(date('d M Y, H:i', strtotime($transaksi['created_at']))) ?></div>
            <div class="small text-muted">Alamat pengiriman</div><div class="fw-semibold mb-3" style="white-space:pre-line"><?= e($transaksi['alamat']) ?></div>
            <div class="small text-muted">Status</div><span class="badge text-bg-<?= e(status_badge_class($transaksi['status'])) ?>"><?= e($transaksi['status']) ?></span>
        </div>
    </div></div>
</div>

<?php include 'views/layouts/footer.php'; ?>

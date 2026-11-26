<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-navy mb-1">Detail Pesanan</h4>
        <p class="text-muted mb-0"><?= e($transaksi['kode_transaksi']) ?></p>
    </div>
    <a href="index.php?page=user-riwayat" class="btn btn-custom-outline">&larr; Riwayat</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-navy">Item Pesanan</h6>
                <span class="badge text-bg-<?= e(status_badge_class($transaksi['status'])) ?>">
                    <?= e($transaksi['status']) ?>
                </span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= e($item['nama_barang'] ?? '(Produk dihapus)') ?></div>
                                    <small class="text-muted">SKU: <?= e($item['kode_sku'] ?? '-') ?></small>
                                </td>
                                <td><?= format_rupiah($item['harga'] ?? 0) ?></td>
                                <td><?= (int) $item['jumlah'] ?> pcs</td>
                                <td class="text-end fw-semibold text-price"><?= format_rupiah($item['subtotal']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th class="text-end text-price"><?= format_rupiah($transaksi['total_harga']) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-navy text-white py-3">
                <h6 class="mb-0">Informasi Pengiriman</h6>
            </div>
            <div class="card-body">
                <p class="mb-1 text-muted small">Tanggal Pesanan</p>
                <p class="fw-semibold"><?= e(date('d M Y, H:i', strtotime($transaksi['created_at']))) ?></p>

                <p class="mb-1 text-muted small">Alamat</p>
                <p class="fw-semibold" style="white-space: pre-line;"><?= e($transaksi['alamat']) ?></p>

                <p class="mb-1 text-muted small">Status</p>
                <p class="mb-0">
                    <span class="badge text-bg-<?= e(status_badge_class($transaksi['status'])) ?>">
                        <?= e($transaksi['status']) ?>
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

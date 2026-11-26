<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-navy mb-1">Riwayat Pesanan</h4>
        <p class="text-muted mb-0">Semua pesanan yang pernah Anda buat.</p>
    </div>
    <a href="index.php?page=user-katalog" class="btn btn-custom-outline">&larr; Katalog</a>
</div>

<?php if (empty($transaksiList)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5 text-center">
            <p class="fs-5 mb-2">Belum ada pesanan.</p>
            <p class="text-muted mb-4">Pesanan Anda akan tampil di sini setelah checkout.</p>
            <a href="index.php?page=user-katalog" class="btn btn-custom-primary">Mulai Belanja</a>
        </div>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaksiList as $trx): ?>
                        <tr>
                            <td class="fw-semibold"><?= e($trx['kode_transaksi']) ?></td>
                            <td><?= e(date('d M Y, H:i', strtotime($trx['created_at']))) ?></td>
                            <td class="fw-semibold text-price"><?= format_rupiah($trx['total_harga']) ?></td>
                            <td>
                                <span class="badge text-bg-<?= e(status_badge_class($trx['status'])) ?>">
                                    <?= e($trx['status']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="index.php?page=user-transaksi-detail&id=<?= (int) $trx['id_transaksi'] ?>"
                                   class="btn btn-sm btn-custom-outline">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

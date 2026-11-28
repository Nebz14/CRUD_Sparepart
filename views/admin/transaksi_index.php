<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-4"><h4 class="fw-bold text-navy mb-1">Kelola Transaksi</h4><p class="text-muted mb-0">Periksa pesanan pelanggan dan perbarui statusnya.</p></div>

<div class="card border-0 shadow-sm"><div class="table-responsive">
<table class="table table-hover align-middle mb-0">
<thead class="table-light"><tr><th>Kode</th><th>Pelanggan</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Detail</th><th>Ubah Status</th></tr></thead>
<tbody>
<?php if (!$transaksiList): ?><tr><td colspan="7" class="text-center text-muted py-4">Belum ada transaksi.</td></tr>
<?php else: foreach ($transaksiList as $trx): ?>
<tr>
    <td class="fw-semibold"><?= e($trx['kode_transaksi']) ?></td>
    <td><?= e($trx['nama']) ?><small class="d-block text-muted"><?= e($trx['username']) ?></small></td>
    <td><?= e(date('d/m/Y H:i', strtotime($trx['created_at']))) ?></td>
    <td class="text-nowrap"><?= format_rupiah($trx['total_harga']) ?></td>
    <td><span class="badge text-bg-<?= e(status_badge_class($trx['status'])) ?>"><?= e($trx['status']) ?></span></td>
    <td><a class="btn btn-sm btn-custom-outline" href="index.php?page=admin-transaksi-detail&id=<?= (int)$trx['id_transaksi'] ?>">Lihat</a></td>
    <td>
        <?php if ($trx['status'] === 'Dibatalkan'): ?><span class="small text-muted">Final</span>
        <?php else: ?><form action="index.php?page=admin-transaksi-status" method="POST" class="d-flex gap-1">
            <input type="hidden" name="id_transaksi" value="<?= (int)$trx['id_transaksi'] ?>">
            <select class="form-select form-select-sm" name="status" aria-label="Status transaksi">
                <?php foreach (['Menunggu Konfirmasi','Diproses','Selesai','Dibatalkan'] as $status): ?>
                    <option value="<?= e($status) ?>" <?= $trx['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                <?php endforeach; ?>
            </select><button class="btn btn-sm btn-custom-primary" onclick="return confirm('Simpan perubahan status pesanan?')">Simpan</button>
        </form><?php endif; ?>
    </td>
</tr>
<?php endforeach; endif; ?>
</tbody></table></div></div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

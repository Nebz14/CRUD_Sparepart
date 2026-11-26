<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-navy mb-1">Keranjang Belanja</h4>
        <p class="text-muted mb-0">Periksa item pesanan Anda sebelum checkout.</p>
    </div>
    <a href="index.php?page=user-katalog" class="btn btn-custom-outline">&larr; Lanjut Belanja</a>
</div>

<?php if (empty($items)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5 text-center">
            <p class="fs-5 mb-2">Keranjang Anda masih kosong.</p>
            <p class="text-muted mb-4">Yuk pilih sparepart yang Anda butuhkan.</p>
            <a href="index.php?page=user-katalog" class="btn btn-custom-primary">Lihat Katalog</a>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-navy">Item Pesanan (<?= count($items) ?>)</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th style="width: 150px;">Jumlah</th>
                                <th>Subtotal</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <?php $produk = $item['produk']; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if (!empty($produk['gambar_url'])): ?>
                                                <img src="<?= e($produk['gambar_url']) ?>"
                                                     alt="<?= e($produk['nama_barang']) ?>" class="table-thumb">
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-semibold"><?= e($produk['nama_barang']) ?></div>
                                                <small class="text-muted">SKU: <?= e($produk['kode_sku']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= format_rupiah($produk['harga']) ?></td>
                                    <td>
                                        <form action="index.php?page=user-cart-update" method="POST" class="d-flex gap-1">
                                            <input type="hidden" name="id_produk" value="<?= (int) $produk['id_produk'] ?>">
                                            <input type="number" class="form-control form-control-sm" name="jumlah"
                                                   value="<?= (int) $item['jumlah'] ?>" min="1"
                                                   max="<?= (int) $produk['stok'] ?>" style="width: 80px;">
                                            <button type="submit" class="btn btn-sm btn-custom-outline">Update</button>
                                        </form>
                                    </td>
                                    <td class="fw-semibold text-price"><?= format_rupiah($item['subtotal']) ?></td>
                                    <td class="text-end">
                                        <form action="index.php?page=user-cart-delete" method="POST"
                                              onsubmit="return confirm('Hapus item ini dari keranjang?');">
                                            <input type="hidden" name="id_produk" value="<?= (int) $produk['id_produk'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-navy text-white py-3">
                    <h6 class="mb-0">Ringkasan Pesanan</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Item</span>
                        <strong><?= (int) array_sum(array_column($items, 'jumlah')) ?> pcs</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Total Harga</span>
                        <strong class="text-price"><?= format_rupiah($total) ?></strong>
                    </div>

                    <hr>

                    <form action="index.php?page=user-checkout" method="POST">
                        <div class="mb-3">
                            <label for="alamat" class="form-label text-muted">Alamat Pengiriman</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3"
                                      minlength="5" placeholder="Nama jalan, nomor rumah, kota..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-custom-primary w-100 py-2">
                            Buat Pesanan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

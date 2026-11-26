<?php include 'views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h4 class="fw-bold text-navy mb-1"><?= !empty($produk['id_produk']) ? 'Edit Produk' : 'Tambah Produk' ?></h4><p class="text-muted mb-0">Lengkapi informasi produk di bawah ini.</p></div>
    <a href="index.php?page=admin-produk" class="btn btn-custom-outline">&larr; Kembali</a>
</div>

<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>

<div class="card border-0 shadow-sm"><div class="card-body p-4">
<form action="<?= e($formAction) ?>" method="POST" enctype="multipart/form-data">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label" for="kode_sku">Kode SKU</label><input class="form-control" id="kode_sku" name="kode_sku" maxlength="50" value="<?= e($produk['kode_sku'] ?? '') ?>" required></div>
        <div class="col-md-6"><label class="form-label" for="nama_barang">Nama Barang</label><input class="form-control" id="nama_barang" name="nama_barang" maxlength="150" value="<?= e($produk['nama_barang'] ?? '') ?>" required></div>
        <div class="col-md-4"><label class="form-label" for="kategori">Kategori</label><select class="form-select" id="kategori" name="kategori" required>
            <option value="Sparepart" <?= ($produk['kategori'] ?? '') === 'Sparepart' ? 'selected' : '' ?>>Sparepart</option>
            <option value="Aksesoris" <?= ($produk['kategori'] ?? '') === 'Aksesoris' ? 'selected' : '' ?>>Aksesoris</option>
        </select></div>
        <div class="col-md-4"><label class="form-label" for="harga">Harga (Rp)</label><input class="form-control" type="number" id="harga" name="harga" min="0" step="1" value="<?= e($produk['harga'] ?? '') ?>" required></div>
        <div class="col-md-4"><label class="form-label" for="stok">Stok</label><input class="form-control" type="number" id="stok" name="stok" min="0" step="1" value="<?= e($produk['stok'] ?? '') ?>" required></div>
        <div class="col-12"><label class="form-label" for="gambar">Gambar Produk (JPG/PNG/WEBP, maks. 2 MB)</label><input class="form-control" type="file" id="gambar" name="gambar" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
            <?php if (!empty($produk['gambar_url'])): ?><div class="mt-2"><img src="<?= e($produk['gambar_url']) ?>" alt="Gambar produk saat ini" style="max-width:150px;max-height:120px;object-fit:contain" class="rounded border p-1"><div class="form-text">Kosongkan jika tidak ingin mengganti gambar.</div></div><?php endif; ?>
        </div>
    </div>
    <div class="d-flex gap-2 mt-4"><button type="submit" class="btn btn-custom-primary"><?= !empty($produk['id_produk']) ? 'Simpan Perubahan' : 'Simpan Produk' ?></button><a href="index.php?page=admin-produk" class="btn btn-outline-secondary">Batal</a></div>
</form>
</div></div>

<?php include 'views/layouts/footer.php'; ?>

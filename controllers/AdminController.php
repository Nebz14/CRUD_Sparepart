<?php
/**
 * Kerangka controller Admin.
 *
 * File ini disiapkan agar route admin-* di index.php tidak error sebelum
 * rekan tim menambahkan implementasi aslinya. Silakan ganti isi method
 * dengan logic CRUD produk / transaksi sesuai kebutuhan.
 */
class AdminController
{
    private function renderPlaceholder(string $title, string $description): void
    {
        $pageTitle = $title . ' - BengkelShop';

        require __DIR__ . '/../views/layouts/header.php';
        ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-navy mb-1"><?= e($title) ?></h4>
                <p class="text-muted mb-0"><?= e($description) ?></p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 text-center">
                <p class="mb-1 fw-semibold">Halaman ini masih berupa kerangka.</p>
                <p class="text-muted mb-0">Implementasi fitur Admin akan ditambahkan oleh rekan tim.</p>
            </div>
        </div>
        <?php
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function dashboard(): void
    {
        $this->renderPlaceholder('Dashboard Admin', 'Ringkasan data toko, produk, dan transaksi.');
    }

    public function produk(): void
    {
        $this->renderPlaceholder('Kelola Produk', 'Daftar produk beserta aksi tambah, edit, dan hapus.');
    }

    public function produkTambah(): void
    {
        $this->renderPlaceholder('Tambah Produk', 'Form untuk menambahkan produk baru.');
    }

    public function produkEdit(): void
    {
        $this->renderPlaceholder('Edit Produk', 'Form untuk memperbarui data produk.');
    }

    public function produkHapus(): void
    {
        $this->renderPlaceholder('Hapus Produk', 'Proses penghapusan produk.');
    }

    public function transaksi(): void
    {
        $this->renderPlaceholder('Kelola Transaksi', 'Daftar transaksi dari seluruh user.');
    }

    public function transaksiDetail(): void
    {
        $this->renderPlaceholder('Detail Transaksi', 'Rincian item dan data pengiriman pesanan.');
    }

    public function transaksiStatus(): void
    {
        $this->renderPlaceholder('Ubah Status Transaksi', 'Proses perubahan status pesanan.');
    }
}

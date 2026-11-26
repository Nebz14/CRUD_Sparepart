<?php
/**
 * Kerangka controller Owner.
 *
 * Disiapkan agar route owner-dashboard di index.php tidak error sebelum
 * rekan tim menambahkan implementasi aslinya.
 */
class OwnerController
{
    public function dashboard(): void
    {
        $pageTitle = 'Dashboard Owner - BengkelShop';

        require __DIR__ . '/../views/layouts/header.php';
        ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-navy mb-1">Dashboard Owner</h4>
                <p class="text-muted mb-0">Laporan penjualan dan performa toko.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 text-center">
                <p class="mb-1 fw-semibold">Halaman ini masih berupa kerangka.</p>
                <p class="text-muted mb-0">Implementasi fitur Owner akan ditambahkan oleh rekan tim.</p>
            </div>
        </div>
        <?php
        require __DIR__ . '/../views/layouts/footer.php';
    }
}

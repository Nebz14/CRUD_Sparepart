<?php
/**
 * Controller fitur user: katalog, keranjang, checkout, riwayat pesanan.
 */
class UserController
{
    private ProductModel $productModel;
    private TransactionModel $transactionModel;

    public function __construct()
    {
        $this->productModel     = new ProductModel();
        $this->transactionModel = new TransactionModel();
    }

    /**
     * Katalog produk + pencarian + filter kategori (boleh diakses tamu).
     */
    public function katalog(): void
    {
        $search   = trim($_GET['q'] ?? '');
        $kategori = trim($_GET['kategori'] ?? '');

        $produkList   = $this->productModel->all($search, $kategori);
        $kategoriList = $this->productModel->kategoriList();

        $backParams = [];
        if ($search !== '') {
            $backParams['q'] = $search;
        }
        if ($kategori !== '') {
            $backParams['kategori'] = $kategori;
        }
        $backUrl = 'index.php?page=user-katalog';
        if (!empty($backParams)) {
            $backUrl .= '&' . http_build_query($backParams);
        }

        $pageTitle = 'Katalog Produk - BengkelShop';
        require __DIR__ . '/../views/user/katalog.php';
    }

    public function keranjang(): void
    {
        require_role('user');

        $cart  = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $items = [];
        $total = 0;
        $changed = false;

        foreach ($cart as $idProduk => $jumlah) {
            $produk = $this->productModel->find((int) $idProduk);

            if (!$produk || (int) $produk['stok'] < 1) {
                unset($_SESSION['cart'][$idProduk]);
                $changed = true;
                continue;
            }

            $jumlah = (int) $jumlah;

            if ($jumlah < 1) {
                unset($_SESSION['cart'][$idProduk]);
                $changed = true;
                continue;
            }

            if ($jumlah > (int) $produk['stok']) {
                $jumlah = (int) $produk['stok'];
                $_SESSION['cart'][$idProduk] = $jumlah;
                $changed = true;
            }

            $subtotal = (int) $produk['harga'] * $jumlah;
            $total   += $subtotal;

            $items[] = [
                'produk'   => $produk,
                'jumlah'   => $jumlah,
                'subtotal' => $subtotal,
            ];
        }

        if ($changed) {
            set_flash('danger', 'Beberapa item di keranjang disesuaikan dengan stok yang tersedia.');
        }

        $pageTitle = 'Keranjang Belanja - BengkelShop';
        require __DIR__ . '/../views/user/keranjang.php';
    }

    public function cartAdd(): void
    {
        require_role('user');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=user-katalog');
        }

        $idProduk = (int) ($_POST['id_produk'] ?? 0);
        $jumlah   = (int) ($_POST['jumlah'] ?? 1);
        $back     = safe_back_target($_POST['back'] ?? '');

        if ($jumlah < 1) {
            $jumlah = 1;
        }

        $produk = $this->productModel->find($idProduk);

        if (!$produk || (int) $produk['stok'] < 1) {
            set_flash('danger', 'Produk tidak tersedia atau stok habis.');
            redirect($back);
        }

        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $jumlahBaru = (int) ($_SESSION['cart'][$idProduk] ?? 0) + $jumlah;

        if ($jumlahBaru > (int) $produk['stok']) {
            $jumlahBaru = (int) $produk['stok'];
            set_flash('danger', 'Jumlah melebihi stok. Keranjang diisi maksimal ' . $jumlahBaru . ' pcs.');
        } else {
            set_flash('success', $produk['nama_barang'] . ' ditambahkan ke keranjang.');
        }

        $_SESSION['cart'][$idProduk] = $jumlahBaru;

        redirect($back);
    }

    public function cartUpdate(): void
    {
        require_role('user');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=user-keranjang');
        }

        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $quantities = $_POST['qty'] ?? [];
        $updated    = false;

        // Dukung update banyak item sekaligus (qty[id_produk] = jumlah).
        if (is_array($quantities)) {
            foreach ($quantities as $idProduk => $jumlah) {
                $idProduk = (int) $idProduk;
                $jumlah   = (int) $jumlah;
                $produk   = $this->productModel->find($idProduk);

                if (!$produk) {
                    unset($_SESSION['cart'][$idProduk]);
                    $updated = true;
                    continue;
                }

                if ($jumlah < 1) {
                    unset($_SESSION['cart'][$idProduk]);
                } elseif ($jumlah > (int) $produk['stok']) {
                    $_SESSION['cart'][$idProduk] = (int) $produk['stok'];
                } else {
                    $_SESSION['cart'][$idProduk] = $jumlah;
                }

                $updated = true;
            }
        }

        // Dukung update satu item (id_produk + jumlah).
        if (isset($_POST['id_produk'])) {
            $idProduk = (int) $_POST['id_produk'];
            $jumlah   = (int) ($_POST['jumlah'] ?? 0);
            $produk   = $this->productModel->find($idProduk);

            if ($produk) {
                if ($jumlah < 1) {
                    unset($_SESSION['cart'][$idProduk]);
                } elseif ($jumlah > (int) $produk['stok']) {
                    $_SESSION['cart'][$idProduk] = (int) $produk['stok'];
                } else {
                    $_SESSION['cart'][$idProduk] = $jumlah;
                }

                $updated = true;
            }
        }

        set_flash(
            $updated ? 'success' : 'danger',
            $updated ? 'Keranjang berhasil diperbarui.' : 'Tidak ada perubahan pada keranjang.'
        );

        redirect('index.php?page=user-keranjang');
    }

    public function cartDelete(): void
    {
        require_role('user');

        $idProduk = (int) ($_POST['id_produk'] ?? $_GET['id'] ?? 0);

        if (isset($_SESSION['cart'][$idProduk])) {
            unset($_SESSION['cart'][$idProduk]);
            set_flash('success', 'Item berhasil dihapus dari keranjang.');
        } else {
            set_flash('danger', 'Item tidak ditemukan di keranjang.');
        }

        redirect('index.php?page=user-keranjang');
    }

    /**
     * Checkout: simpan tb_transaksi + tb_detail_transaksi, kurangi stok.
     */
    public function checkout(): void
    {
        require_role('user');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=user-keranjang');
        }

        $alamat = trim($_POST['alamat'] ?? '');
        $cart   = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];

        if (empty($cart)) {
            set_flash('danger', 'Keranjang masih kosong.');
            redirect('index.php?page=user-keranjang');
        }

        if (mb_strlen($alamat) < 5) {
            set_flash('danger', 'Alamat pengiriman minimal 5 karakter.');
            redirect('index.php?page=user-keranjang');
        }

        $result = $this->transactionModel->createOrder(
            (int) $_SESSION['user_id'],
            $alamat,
            $cart
        );

        if (!$result['success']) {
            set_flash('danger', $result['message'] ?? 'Checkout gagal.');
            redirect('index.php?page=user-keranjang');
        }

        unset($_SESSION['cart']);

        set_flash('success', 'Pesanan ' . $result['kode_transaksi'] . ' berhasil dibuat.');
        redirect('index.php?page=user-transaksi-detail&id=' . (int) $result['id_transaksi']);
    }

    public function riwayat(): void
    {
        require_role('user');

        $transaksiList = $this->transactionModel->getByUser((int) $_SESSION['user_id']);

        $pageTitle = 'Riwayat Pesanan - BengkelShop';
        require __DIR__ . '/../views/user/riwayat.php';
    }

    public function transaksiDetail(): void
    {
        require_role('user');

        $idTransaksi = (int) ($_GET['id'] ?? 0);
        $transaksi   = $this->transactionModel->findForUser($idTransaksi, (int) $_SESSION['user_id']);

        if (!$transaksi) {
            set_flash('danger', 'Pesanan tidak ditemukan.');
            redirect('index.php?page=user-riwayat');
        }

        $items = $this->transactionModel->getItems($idTransaksi);

        $pageTitle = 'Detail Pesanan - BengkelShop';
        require __DIR__ . '/../views/user/detail_transaksi.php';
    }
}

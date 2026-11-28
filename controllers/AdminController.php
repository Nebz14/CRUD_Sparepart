<?php
require_once __DIR__ . '/../models/ProdukModel.php';
require_once __DIR__ . '/../models/TransaksiModel.php';

class AdminController
{
    private ProdukModel $produkModel;
    private TransaksiModel $transaksiModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (($_SESSION['role'] ?? null) !== 'admin') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Akses ditolak!'];
            header('Location: index.php?page=login');
            exit;
        }
        $this->produkModel = new ProdukModel();
        $this->transaksiModel = new TransaksiModel();
    }

    private function render(string $view, string $title, array $data = []): void
    {
        $pageTitle = $title . ' - BengkelShop';

        // Variabel dari method pemanggil tidak otomatis terlihat di dalam
        // view. Ekstrak data yang dikirim agar view bisa memakainya.
        extract($data, EXTR_SKIP);

        require __DIR__ . '/../views/admin/' . $view . '.php';
    }

    private function uploadGambar(?array $file, ?string $gambarLama = null): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $gambarLama;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload gambar gagal. Periksa ukuran file.');
        }
        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            throw new RuntimeException('Ukuran gambar maksimal 2 MB.');
        }

        $tmp = $file['tmp_name'] ?? '';
        if (!is_uploaded_file($tmp)) {
            throw new RuntimeException('File gambar tidak valid.');
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($extensions[$mime])) {
            throw new RuntimeException('Format gambar harus JPG, PNG, atau WEBP.');
        }

        $folder = dirname(__DIR__) . '/img';
        if (!is_dir($folder) && !mkdir($folder, 0775, true) && !is_dir($folder)) {
            throw new RuntimeException('Folder img/ tidak dapat dibuat.');
        }

        $filename = 'produk_' . bin2hex(random_bytes(8)) . '.' . $extensions[$mime];
        if (!move_uploaded_file($tmp, $folder . '/' . $filename)) {
            throw new RuntimeException('Gagal menyimpan gambar ke folder img/.');
        }
        return 'img/' . $filename;
    }

    private function hapusFileGambar(?string $path): void
    {
        if (!$path || strpos($path, 'img/') !== 0) return;
        $base = realpath(dirname(__DIR__) . '/img');
        $file = realpath(dirname(__DIR__) . '/' . $path);
        if ($base && $file && strpos($file, $base . DIRECTORY_SEPARATOR) === 0 && is_file($file)) {
            @unlink($file);
        }
    }

    private function validasiProduk(array $post): array
    {
        $data = [
            'kode_sku' => strtoupper(trim($post['kode_sku'] ?? '')),
            'nama_barang' => trim($post['nama_barang'] ?? ''),
            'kategori' => trim($post['kategori'] ?? ''),
            'harga' => filter_var($post['harga'] ?? null, FILTER_VALIDATE_INT),
            'stok' => filter_var($post['stok'] ?? null, FILTER_VALIDATE_INT),
        ];
        if ($data['kode_sku'] === '' || $data['nama_barang'] === '') {
            throw new RuntimeException('Kode SKU dan nama barang wajib diisi.');
        }
        if (!in_array($data['kategori'], ['Sparepart', 'Aksesoris'], true)) {
            throw new RuntimeException('Kategori produk tidak valid.');
        }
        if ($data['harga'] === false || $data['harga'] < 0) {
            throw new RuntimeException('Harga harus berupa angka minimal 0.');
        }
        if ($data['stok'] === false || $data['stok'] < 0) {
            throw new RuntimeException('Stok harus berupa angka minimal 0.');
        }
        return $data;
    }

    public function dashboard(): void
    {
        $ringkasan = $this->transaksiModel->ringkasanOmzet();
        $jumlahProduk = $this->produkModel->countAll();
        $stokMenipis = $this->produkModel->lowStock();
        $this->render('dashboard', 'Dashboard Admin', compact('ringkasan', 'jumlahProduk', 'stokMenipis'));
    }

    public function produk(): void
    {
        $search = trim($_GET['q'] ?? '');
        $kategori = trim($_GET['kategori'] ?? '');
        $produkList = $this->produkModel->all($search, $kategori);
        $this->render('produk_index', 'Kelola Produk', compact('search', 'kategori', 'produkList'));
    }

    public function produkTambah(): void
    {
        $produk = [
            'id_produk' => 0, 'kode_sku' => '', 'nama_barang' => '',
            'kategori' => 'Sparepart', 'harga' => '', 'stok' => '', 'gambar_url' => null,
        ];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $gambarBaru = null;
            try {
                $data = $this->validasiProduk($_POST);
                $gambarBaru = $this->uploadGambar($_FILES['gambar'] ?? null);
                $data['gambar_url'] = $gambarBaru;
                $this->produkModel->create($data);
                set_flash('success', 'Produk berhasil ditambahkan.');
                redirect('index.php?page=admin-produk');
            } catch (PDOException $e) {
                if ($gambarBaru) $this->hapusFileGambar($gambarBaru);
                $errors[] = $e->getCode() === '23000'
                    ? 'Kode SKU sudah digunakan. Gunakan kode SKU lain.'
                    : 'Gagal menyimpan produk ke database.';
            } catch (Throwable $e) {
                if ($gambarBaru) $this->hapusFileGambar($gambarBaru);
                $errors[] = $e->getMessage();
            }
            $produk = array_merge($produk, $_POST);
        }
        $formAction = 'index.php?page=admin-produk-tambah';
        $this->render('produk_form', 'Tambah Produk', compact('produk', 'errors', 'formAction'));
    }

    public function produkEdit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            set_flash('danger', 'Produk tidak ditemukan.');
            redirect('index.php?page=admin-produk');
        }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $gambarBaru = null;
            try {
                $data = $this->validasiProduk($_POST);
                $gambarBaru = $this->uploadGambar($_FILES['gambar'] ?? null, $produk['gambar_url']);
                $data['gambar_url'] = $gambarBaru;
                $this->produkModel->update($id, $data);
                if ($gambarBaru !== $produk['gambar_url']) $this->hapusFileGambar($produk['gambar_url']);
                set_flash('success', 'Produk berhasil diperbarui.');
                redirect('index.php?page=admin-produk');
            } catch (PDOException $e) {
                if ($gambarBaru && $gambarBaru !== $produk['gambar_url']) $this->hapusFileGambar($gambarBaru);
                $errors[] = $e->getCode() === '23000'
                    ? 'Kode SKU sudah digunakan produk lain.'
                    : 'Gagal memperbarui produk.';
            } catch (Throwable $e) {
                if ($gambarBaru && $gambarBaru !== $produk['gambar_url']) $this->hapusFileGambar($gambarBaru);
                $errors[] = $e->getMessage();
            }
            $produk = array_merge($produk, $_POST);
            $produk['gambar_url'] = $produk['gambar_url'] ?? null;
        }
        $formAction = 'index.php?page=admin-produk-edit&id=' . $id;
        $this->render('produk_form', 'Edit Produk', compact('produk', 'errors', 'formAction'));
    }

    public function produkHapus(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            set_flash('danger', 'Produk tidak ditemukan.');
            redirect('index.php?page=admin-produk');
        }
        try {
            $this->produkModel->delete($id);
            $this->hapusFileGambar($produk['gambar_url']);
            set_flash('success', 'Produk berhasil dihapus.');
        } catch (PDOException $e) {
            set_flash('danger', 'Produk tidak dapat dihapus karena sudah digunakan dalam transaksi.');
        }
        redirect('index.php?page=admin-produk');
    }

    public function transaksi(): void
    {
        $transaksiList = $this->transaksiModel->semuaTransaksi();
        $this->render('transaksi_index', 'Kelola Transaksi', compact('transaksiList'));
    }

    public function transaksiDetail(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $transaksi = $this->transaksiModel->find($id);
        if (!$transaksi) {
            set_flash('danger', 'Transaksi tidak ditemukan.');
            redirect('index.php?page=admin-transaksi');
        }
        $items = $this->transaksiModel->detailItem($id);
        $this->render('detail_transaksi', 'Detail Transaksi', compact('transaksi', 'items'));
    }

    public function transaksiStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin-transaksi');
        }
        $id = (int) ($_POST['id_transaksi'] ?? 0);
        $status = trim($_POST['status'] ?? '');
        $result = $this->transaksiModel->updateStatus($id, $status);
        set_flash($result['success'] ? 'success' : 'danger', $result['message']);
        redirect('index.php?page=admin-transaksi');
    }
}

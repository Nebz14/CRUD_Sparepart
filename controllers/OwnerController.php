<?php
require_once __DIR__ . '/../models/ProdukModel.php';
require_once __DIR__ . '/../models/TransaksiModel.php';

class OwnerController
{
    private ProdukModel $produkModel;
    private TransaksiModel $transaksiModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (($_SESSION['role'] ?? null) !== 'owner') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Akses ditolak!'];
            header('Location: index.php?page=login');
            exit;
        }
        $this->produkModel = new ProdukModel();
        $this->transaksiModel = new TransaksiModel();
    }

    public function dashboard(): void
    {
        $ringkasan = $this->transaksiModel->ringkasanOmzet();
        $stokMenipis = $this->produkModel->lowStock(5);
        $transaksiList = array_slice($this->transaksiModel->semuaTransaksi(), 0, 8);
        $pageTitle = 'Dashboard Owner - BengkelShop';
        require __DIR__ . '/../views/owner/dashboard.php';
    }
}

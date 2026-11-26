<?php
/**
 * Model laporan dan pengelolaan transaksi untuk Admin/Owner.
 */
require_once __DIR__ . '/../config/database.php';

class TransaksiModel
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function semuaTransaksi(): array
    {
        $stmt = $this->pdo->query(
            'SELECT t.id_transaksi, t.kode_transaksi, t.id_user, t.total_harga,
                    t.alamat, t.status, t.created_at, u.nama, u.username
             FROM tb_transaksi t
             INNER JOIN tb_users u ON u.id_user = t.id_user
             ORDER BY t.created_at DESC, t.id_transaksi DESC'
        );
        return $stmt->fetchAll();
    }

    public function find(int $idTransaksi): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT t.id_transaksi, t.kode_transaksi, t.id_user, t.total_harga,
                    t.alamat, t.status, t.created_at, u.nama, u.username
             FROM tb_transaksi t
             INNER JOIN tb_users u ON u.id_user = t.id_user
             WHERE t.id_transaksi = :id LIMIT 1'
        );
        $stmt->execute([':id' => $idTransaksi]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function detailItem(int $idTransaksi): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT d.id_detail, d.id_transaksi, d.id_produk, d.jumlah, d.subtotal,
                    p.kode_sku, p.nama_barang, p.kategori, p.harga, p.gambar_url
             FROM tb_detail_transaksi d
             LEFT JOIN tb_produk p ON p.id_produk = d.id_produk
             WHERE d.id_transaksi = :id ORDER BY d.id_detail ASC'
        );
        $stmt->execute([':id' => $idTransaksi]);
        return $stmt->fetchAll();
    }

    public function ringkasanOmzet(): array
    {
        $stmt = $this->pdo->query(
            "SELECT
                COALESCE(SUM(CASE WHEN status = 'Selesai' THEN total_harga ELSE 0 END), 0) AS omzet,
                COUNT(*) AS total_transaksi,
                SUM(status = 'Menunggu Konfirmasi') AS menunggu,
                SUM(status = 'Diproses') AS diproses,
                SUM(status = 'Selesai') AS selesai,
                SUM(status = 'Dibatalkan') AS dibatalkan
             FROM tb_transaksi"
        );
        $data = $stmt->fetch() ?: [];
        foreach (['omzet', 'total_transaksi', 'menunggu', 'diproses', 'selesai', 'dibatalkan'] as $key) {
            $data[$key] = (int) ($data[$key] ?? 0);
        }
        return $data;
    }

    /**
     * Mengubah status. Saat pertama kali menjadi Dibatalkan, stok tiap item
     * dikembalikan dalam transaksi database yang sama. Pembatalan kedua kali
     * tidak akan menambah stok lagi.
     */
    public function updateStatus(int $idTransaksi, string $status): array
    {
        $allowed = ['Menunggu Konfirmasi', 'Diproses', 'Selesai', 'Dibatalkan'];
        if (!in_array($status, $allowed, true)) {
            return ['success' => false, 'message' => 'Status transaksi tidak valid.'];
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                'SELECT id_transaksi, status FROM tb_transaksi
                 WHERE id_transaksi = :id FOR UPDATE'
            );
            $stmt->execute([':id' => $idTransaksi]);
            $transaksi = $stmt->fetch();

            if (!$transaksi) {
                throw new RuntimeException('Transaksi tidak ditemukan.');
            }

            $statusLama = $transaksi['status'];
            if ($statusLama === 'Dibatalkan' && $status !== 'Dibatalkan') {
                throw new RuntimeException('Pesanan yang sudah dibatalkan tidak dapat diaktifkan kembali.');
            }

            if ($statusLama !== 'Dibatalkan' && $status === 'Dibatalkan') {
                $stmtItems = $this->pdo->prepare(
                    'SELECT id_produk, SUM(jumlah) AS jumlah
                     FROM tb_detail_transaksi
                     WHERE id_transaksi = :id
                     GROUP BY id_produk'
                );
                $stmtItems->execute([':id' => $idTransaksi]);

                $stmtRestore = $this->pdo->prepare(
                    'UPDATE tb_produk SET stok = stok + :jumlah WHERE id_produk = :id'
                );
                foreach ($stmtItems->fetchAll() as $item) {
                    // Produk dapat saja sudah dihapus; item tersebut dilewati.
                    if ($item['id_produk'] !== null) {
                        $stmtRestore->execute([
                            ':jumlah' => (int) $item['jumlah'],
                            ':id' => (int) $item['id_produk'],
                        ]);
                    }
                }
            }

            $stmtUpdate = $this->pdo->prepare(
                'UPDATE tb_transaksi SET status = :status WHERE id_transaksi = :id'
            );
            $stmtUpdate->execute([':status' => $status, ':id' => $idTransaksi]);

            $this->pdo->commit();
            return ['success' => true, 'message' => 'Status transaksi berhasil diperbarui.'];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            return ['success' => false, 'message' => $e instanceof RuntimeException
                ? $e->getMessage()
                : 'Gagal memperbarui status transaksi.'];
        }
    }
}

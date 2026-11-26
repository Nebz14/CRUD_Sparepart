<?php
/**
 * Model transaksi: checkout, riwayat, dan detail pesanan.
 */
class TransactionModel
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * Simpan pesanan (header + detail) dan kurangi stok dalam satu transaksi.
     *
     * @param array<int|string, int|string> $cart Format: [id_produk => jumlah]
     * @return array{success: bool, message?: string, id_transaksi?: int, kode_transaksi?: string, total_harga?: int}
     */
    public function createOrder(int $idUser, string $alamat, array $cart): array
    {
        $cart = array_filter($cart, static function ($qty) {
            return (int) $qty > 0;
        });

        if (empty($cart)) {
            return ['success' => false, 'message' => 'Keranjang masih kosong.'];
        }

        $ids = array_values(array_unique(array_map('intval', array_keys($cart))));
        $placeholders = implode(', ', array_fill(0, count($ids), '?'));

        try {
            $this->pdo->beginTransaction();

            // Kunci baris produk agar stok tidak bentrok saat checkout bersamaan.
            $stmt = $this->pdo->prepare(
                "SELECT id_produk, nama_barang, harga, stok
                 FROM tb_produk
                 WHERE id_produk IN ($placeholders)
                 FOR UPDATE"
            );
            $stmt->execute($ids);

            $produk = [];
            foreach ($stmt->fetchAll() as $row) {
                $produk[(int) $row['id_produk']] = $row;
            }

            $items = [];
            $total = 0;

            foreach ($cart as $idProduk => $jumlah) {
                $idProduk = (int) $idProduk;
                $jumlah   = (int) $jumlah;

                if (!isset($produk[$idProduk])) {
                    throw new RuntimeException('Ada produk di keranjang yang sudah tidak tersedia.');
                }

                $item = $produk[$idProduk];

                if ((int) $item['stok'] < $jumlah) {
                    throw new RuntimeException(
                        'Stok "' . $item['nama_barang'] . '" tidak mencukupi (tersisa ' . (int) $item['stok'] . ').'
                    );
                }

                $subtotal = (int) $item['harga'] * $jumlah;
                $total   += $subtotal;

                $items[] = [
                    'id_produk'   => $idProduk,
                    'nama_barang' => $item['nama_barang'],
                    'jumlah'      => $jumlah,
                    'subtotal'    => $subtotal,
                ];
            }

            if (empty($items) || $total <= 0) {
                throw new RuntimeException('Total pesanan tidak valid.');
            }

            $kodeTransaksi = 'TRX-' . date('Ymd-His') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

            $stmt = $this->pdo->prepare(
                'INSERT INTO tb_transaksi (kode_transaksi, id_user, total_harga, alamat, status)
                 VALUES (:kode_transaksi, :id_user, :total_harga, :alamat, :status)'
            );
            $stmt->execute([
                ':kode_transaksi' => $kodeTransaksi,
                ':id_user'        => $idUser,
                ':total_harga'    => $total,
                ':alamat'         => $alamat,
                ':status'         => 'Menunggu Konfirmasi',
            ]);

            $idTransaksi = (int) $this->pdo->lastInsertId();

            $stmtDetail = $this->pdo->prepare(
                'INSERT INTO tb_detail_transaksi (id_transaksi, id_produk, jumlah, subtotal)
                 VALUES (:id_transaksi, :id_produk, :jumlah, :subtotal)'
            );

            $stmtStok = $this->pdo->prepare(
                'UPDATE tb_produk
                 SET stok = stok - :jumlah_potong
                 WHERE id_produk = :id_produk AND stok >= :jumlah_syarat'
            );

            foreach ($items as $item) {
                $stmtDetail->execute([
                    ':id_transaksi' => $idTransaksi,
                    ':id_produk'    => $item['id_produk'],
                    ':jumlah'       => $item['jumlah'],
                    ':subtotal'     => $item['subtotal'],
                ]);

                $stmtStok->execute([
                    ':jumlah_potong' => $item['jumlah'],
                    ':id_produk'     => $item['id_produk'],
                    ':jumlah_syarat' => $item['jumlah'],
                ]);

                if ($stmtStok->rowCount() < 1) {
                    throw new RuntimeException(
                        'Stok "' . $item['nama_barang'] . '" berubah saat checkout. Silakan coba lagi.'
                    );
                }
            }

            $this->pdo->commit();

            return [
                'success'        => true,
                'id_transaksi'   => $idTransaksi,
                'kode_transaksi' => $kodeTransaksi,
                'total_harga'    => $total,
            ];
        } catch (RuntimeException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return ['success' => false, 'message' => $e->getMessage()];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return ['success' => false, 'message' => 'Gagal memproses pesanan. Silakan coba beberapa saat lagi.'];
        }
    }

    public function getByUser(int $idUser): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_transaksi, kode_transaksi, total_harga, alamat, status, created_at
             FROM tb_transaksi
             WHERE id_user = :id_user
             ORDER BY created_at DESC, id_transaksi DESC'
        );
        $stmt->execute([':id_user' => $idUser]);

        return $stmt->fetchAll();
    }

    /**
     * Pastikan transaksi memang milik user yang sedang login.
     */
    public function findForUser(int $idTransaksi, int $idUser): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_transaksi, kode_transaksi, total_harga, alamat, status, created_at
             FROM tb_transaksi
             WHERE id_transaksi = :id_transaksi AND id_user = :id_user
             LIMIT 1'
        );
        $stmt->execute([
            ':id_transaksi' => $idTransaksi,
            ':id_user'      => $idUser,
        ]);

        $transaksi = $stmt->fetch();

        return $transaksi ?: null;
    }

    public function getItems(int $idTransaksi): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT d.id_detail, d.id_produk, d.jumlah, d.subtotal,
                    p.kode_sku, p.nama_barang, p.gambar_url, p.harga
             FROM tb_detail_transaksi d
             LEFT JOIN tb_produk p ON p.id_produk = d.id_produk
             WHERE d.id_transaksi = :id_transaksi
             ORDER BY d.id_detail ASC'
        );
        $stmt->execute([':id_transaksi' => $idTransaksi]);

        return $stmt->fetchAll();
    }
}

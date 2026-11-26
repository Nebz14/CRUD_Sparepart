<?php
/**
 * Model produk (dipakai fitur user: katalog & keranjang).
 */
class ProductModel
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * Ambil daftar produk dengan pencarian dan filter kategori.
     */
    public function all(string $search = '', string $kategori = ''): array
    {
        $sql = 'SELECT id_produk, kode_sku, nama_barang, kategori, harga, stok, gambar_url, created_at
                FROM tb_produk';
        $where = [];
        $params = [];

        if ($search !== '') {
            // Nama parameter dibuat berbeda agar aman untuk native prepared statement.
            $where[] = '(nama_barang LIKE :q_nama OR kode_sku LIKE :q_sku)';
            $params[':q_nama'] = '%' . $search . '%';
            $params[':q_sku']  = '%' . $search . '%';
        }

        if ($kategori !== '') {
            $where[] = 'kategori = :kategori';
            $params[':kategori'] = $kategori;
        }

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY id_produk DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function find(int $idProduk): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_produk, kode_sku, nama_barang, kategori, harga, stok, gambar_url, created_at
             FROM tb_produk
             WHERE id_produk = :id_produk
             LIMIT 1'
        );
        $stmt->execute([':id_produk' => $idProduk]);
        $produk = $stmt->fetch();

        return $produk ?: null;
    }

    /**
     * Daftar kategori unik untuk dropdown filter.
     */
    public function kategoriList(): array
    {
        $stmt = $this->pdo->query(
            "SELECT DISTINCT kategori
             FROM tb_produk
             WHERE kategori IS NOT NULL AND kategori <> ''
             ORDER BY kategori ASC"
        );

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

<?php
/**
 * Model CRUD produk untuk area Admin dan Owner.
 */
require_once __DIR__ . '/../config/database.php';

class ProdukModel
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function all(string $search = '', string $kategori = ''): array
    {
        $sql = 'SELECT id_produk, kode_sku, nama_barang, kategori, harga, stok, gambar_url
                FROM tb_produk';
        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = '(nama_barang LIKE :nama OR kode_sku LIKE :sku)';
            $params[':nama'] = '%' . $search . '%';
            $params[':sku'] = '%' . $search . '%';
        }
        if ($kategori !== '') {
            $where[] = 'kategori = :kategori';
            $params[':kategori'] = $kategori;
        }
        if ($where) {
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
            'SELECT id_produk, kode_sku, nama_barang, kategori, harga, stok, gambar_url
             FROM tb_produk WHERE id_produk = :id LIMIT 1'
        );
        $stmt->execute([':id' => $idProduk]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO tb_produk (kode_sku, nama_barang, kategori, harga, stok, gambar_url)
             VALUES (:sku, :nama, :kategori, :harga, :stok, :gambar)'
        );
        $stmt->execute([
            ':sku' => $data['kode_sku'],
            ':nama' => $data['nama_barang'],
            ':kategori' => $data['kategori'],
            ':harga' => $data['harga'],
            ':stok' => $data['stok'],
            ':gambar' => $data['gambar_url'] ?? null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $idProduk, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE tb_produk
             SET kode_sku = :sku, nama_barang = :nama, kategori = :kategori,
                 harga = :harga, stok = :stok, gambar_url = :gambar
             WHERE id_produk = :id'
        );
        return $stmt->execute([
            ':sku' => $data['kode_sku'],
            ':nama' => $data['nama_barang'],
            ':kategori' => $data['kategori'],
            ':harga' => $data['harga'],
            ':stok' => $data['stok'],
            ':gambar' => $data['gambar_url'] ?? null,
            ':id' => $idProduk,
        ]);
    }

    public function delete(int $idProduk): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM tb_produk WHERE id_produk = :id');
        return $stmt->execute([':id' => $idProduk]);
    }

    public function lowStock(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_produk, kode_sku, nama_barang, kategori, harga, stok, gambar_url
             FROM tb_produk WHERE stok < :batas ORDER BY stok ASC, nama_barang ASC'
        );
        $stmt->bindValue(':batas', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM tb_produk')->fetchColumn();
    }

    public function tambahStok(int $idProduk, int $jumlah): bool
    {
        if ($jumlah <= 0) return false;
        $stmt = $this->pdo->prepare(
            'UPDATE tb_produk SET stok = stok + :jumlah WHERE id_produk = :id'
        );
        $stmt->execute([':jumlah' => $jumlah, ':id' => $idProduk]);
        return $stmt->rowCount() > 0;
    }

    public function kurangiStok(int $idProduk, int $jumlah): bool
    {
        if ($jumlah <= 0) return false;
        $stmt = $this->pdo->prepare(
            'UPDATE tb_produk SET stok = stok - :jumlah
             WHERE id_produk = :id AND stok >= :jumlah_cek'
        );
        $stmt->execute([
            ':jumlah' => $jumlah,
            ':id' => $idProduk,
            ':jumlah_cek' => $jumlah,
        ]);
        return $stmt->rowCount() > 0;
    }

    // Alias berbahasa Inggris agar mudah dipakai bila dibutuhkan.
    public function increaseStock(int $idProduk, int $jumlah): bool { return $this->tambahStok($idProduk, $jumlah); }
    public function decreaseStock(int $idProduk, int $jumlah): bool { return $this->kurangiStok($idProduk, $jumlah); }
}

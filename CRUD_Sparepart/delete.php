<?php
require_once 'connection.php';

// Cek apakah ada parameter 'id' yang dikirim melalui URL
if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];

    try {
        // 1. Ambil data produk terlebih dahulu untuk mendapatkan path/URL gambar
        $stmt = $pdo->prepare("SELECT gambar_url FROM tb_produk WHERE id_produk = :id");
        $stmt->execute([':id' => $id_produk]);
        $produk = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Jika produk ditemukan dan memiliki gambar, hapus file gambar dari folder
        if ($produk && !empty($produk['gambar_url'])) {
            if (file_exists($produk['gambar_url'])) {
                unlink($produk['gambar_url']); // Menghapus file fisik
            }
        }

        // 3. Hapus data produk dari database
        $stmtDelete = $pdo->prepare("DELETE FROM tb_produk WHERE id_produk = :id");
        $result = $stmtDelete->execute([':id' => $id_produk]);

        if ($result) {
            echo "<script>
                    alert('Data produk berhasil dihapus!');
                    window.location.href='index.php';
                  </script>";
            exit;
        }

    } catch (PDOException $e) {
        echo "<script>
                alert('Gagal menghapus data: " . addslashes($e->getMessage()) . "');
                window.location.href='index.php';
              </script>";
        exit;
    }
} else {
    // Jika tidak ada ID di URL, kembalikan ke halaman index
    header("Location: index.php");
    exit;
}
?>
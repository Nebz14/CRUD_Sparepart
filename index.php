<?php
/**
 * Front controller / router BengkelShop.
 *
 * Semua request masuk ke file ini lalu diteruskan ke controller
 * berdasarkan parameter ?page=...
 */

define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/core/helpers.php';

require_once BASE_PATH . '/models/UserModel.php';
require_once BASE_PATH . '/models/ProductModel.php';
require_once BASE_PATH . '/models/TransactionModel.php';

require_once BASE_PATH . '/controllers/AuthController.php';
require_once BASE_PATH . '/controllers/UserController.php';
require_once BASE_PATH . '/controllers/AdminController.php';
require_once BASE_PATH . '/controllers/OwnerController.php';

/**
 * Daftar route.
 * 'role' bersifat opsional dan dipakai untuk proteksi akses.
 */
$routes = [
    // Auth
    'login'    => ['controller' => 'AuthController', 'method' => 'login'],
    'register' => ['controller' => 'AuthController', 'method' => 'register'],
    'logout'   => ['controller' => 'AuthController', 'method' => 'logout'],

    // Fitur user
    'user-katalog'          => ['controller' => 'UserController', 'method' => 'katalog'],
    'user-keranjang'        => ['controller' => 'UserController', 'method' => 'keranjang', 'role' => 'user'],
    'user-cart-add'         => ['controller' => 'UserController', 'method' => 'cartAdd', 'role' => 'user'],
    'user-cart-update'      => ['controller' => 'UserController', 'method' => 'cartUpdate', 'role' => 'user'],
    'user-cart-delete'      => ['controller' => 'UserController', 'method' => 'cartDelete', 'role' => 'user'],
    'user-checkout'         => ['controller' => 'UserController', 'method' => 'checkout', 'role' => 'user'],
    'user-riwayat'          => ['controller' => 'UserController', 'method' => 'riwayat', 'role' => 'user'],
    'user-transaksi-detail' => ['controller' => 'UserController', 'method' => 'transaksiDetail', 'role' => 'user'],

    // Disiapkan untuk tim Admin (implementasi di controllers/AdminController.php)
    'admin-dashboard'        => ['controller' => 'AdminController', 'method' => 'dashboard', 'role' => 'admin'],
    'admin-produk'           => ['controller' => 'AdminController', 'method' => 'produk', 'role' => 'admin'],
    'admin-produk-tambah'    => ['controller' => 'AdminController', 'method' => 'produkTambah', 'role' => 'admin'],
    'admin-produk-edit'      => ['controller' => 'AdminController', 'method' => 'produkEdit', 'role' => 'admin'],
    'admin-produk-hapus'     => ['controller' => 'AdminController', 'method' => 'produkHapus', 'role' => 'admin'],
    'admin-transaksi'        => ['controller' => 'AdminController', 'method' => 'transaksi', 'role' => 'admin'],
    'admin-transaksi-detail' => ['controller' => 'AdminController', 'method' => 'transaksiDetail', 'role' => 'admin'],
    'admin-transaksi-status' => ['controller' => 'AdminController', 'method' => 'transaksiStatus', 'role' => 'admin'],

    // Disiapkan untuk tim Owner (implementasi di controllers/OwnerController.php)
    'owner-dashboard' => ['controller' => 'OwnerController', 'method' => 'dashboard', 'role' => 'owner'],
];

$page = $_GET['page'] ?? null;

if (!is_string($page) || $page === '') {
    $page = default_page();
}

if (!isset($routes[$page])) {
    http_response_code(404);
    $pageTitle = 'Halaman Tidak Ditemukan - BengkelShop';
    require BASE_PATH . '/views/errors/404.php';
    exit;
}

$route = $routes[$page];

if (!empty($route['role'])) {
    require_role($route['role']);
}

/** @var object $controller */
$controller = new $route['controller']();
$controller->{$route['method']}();

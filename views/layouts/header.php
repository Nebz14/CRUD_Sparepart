<?php
/**
 * Layout header: HTML head, navbar dinamis, dan alert flash.
 *
 * Variabel opsional: $pageTitle
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle   = $pageTitle ?? 'BengkelShop';
$currentPage = $_GET['page'] ?? '';
$flash       = take_flash();
$loggedIn    = is_logged_in();
$role        = current_role();
$cartCount   = cart_items_count();
$homeUrl     = 'index.php?page=' . home_for_role();

$roleLabel = [
    'admin' => 'Admin',
    'user'  => 'User',
    'owner' => 'Owner',
][$role] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --dark-navy: #202940;
            --dark-brown: #4B4038;
            --light-brown: #9A8678;
            --beige: #CAAA98;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1 0 auto;
        }

        .navbar-custom { background-color: var(--dark-navy); }
        .bg-navy { background-color: var(--dark-navy); }
        .text-navy { color: var(--dark-navy); }
        .text-price { color: var(--dark-brown); font-weight: bold; font-size: 1.1rem; }

        .btn-custom-primary {
            background-color: var(--light-brown);
            color: #fff;
            border: none;
        }
        .btn-custom-primary:hover,
        .btn-custom-primary:focus {
            background-color: var(--dark-brown);
            color: #fff;
        }

        .btn-custom-outline {
            border: 1px solid var(--light-brown);
            color: var(--dark-brown);
        }
        .btn-custom-outline:hover,
        .btn-custom-outline:focus {
            background-color: var(--beige);
            color: var(--dark-brown);
        }

        .product-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            border-radius: 12px;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(32, 41, 64, 0.15) !important;
        }

        .card-img-wrapper {
            height: 200px;
            overflow: hidden;
            border-radius: 12px 12px 0 0;
            background-color: #eee;
        }
        .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .badge-category {
            background-color: var(--beige);
            color: var(--dark-brown);
            font-weight: 500;
        }

        .auth-card { border-radius: 14px; overflow: hidden; }
        .table-thumb {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 8px;
            background-color: #eee;
        }

        footer.app-footer {
            background-color: var(--dark-navy);
            color: rgba(255, 255, 255, 0.65);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= e($homeUrl) ?>">🔧 BengkelShop</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'admin-dashboard' ? 'active' : '' ?>"
                           href="index.php?page=admin-dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'admin-produk' ? 'active' : '' ?>"
                           href="index.php?page=admin-produk">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'admin-transaksi' ? 'active' : '' ?>"
                           href="index.php?page=admin-transaksi">Transaksi</a>
                    </li>
                <?php elseif ($role === 'owner'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'owner-dashboard' ? 'active' : '' ?>"
                           href="index.php?page=owner-dashboard">Dashboard Owner</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'user-katalog' ? 'active' : '' ?>"
                           href="index.php?page=user-katalog">Katalog</a>
                    </li>
                    <?php if ($role === 'user'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentPage === 'user-keranjang' ? 'active' : '' ?>"
                               href="index.php?page=user-keranjang">
                                Keranjang
                                <?php if ($cartCount > 0): ?>
                                    <span class="badge rounded-pill text-bg-warning"><?= (int) $cartCount ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentPage === 'user-riwayat' ? 'active' : '' ?>"
                               href="index.php?page=user-riwayat">Riwayat</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto align-items-lg-center">
                <?php if ($loggedIn): ?>
                    <li class="nav-item">
                        <span class="navbar-text small text-white-50">
                            Halo, <strong class="text-white"><?= e($_SESSION['nama'] ?? '') ?></strong>
                            (<?= e($roleLabel) ?>)
                        </span>
                    </li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a href="index.php?page=logout" class="btn btn-sm btn-outline-light w-100">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item mt-2 mt-lg-0">
                        <a href="index.php?page=login" class="btn btn-sm btn-outline-light w-100">Masuk</a>
                    </li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a href="index.php?page=register" class="btn btn-sm btn-custom-primary w-100">Daftar</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container pb-5">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

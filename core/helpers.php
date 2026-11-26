<?php
/**
 * Helper global BengkelShop.
 *
 * Berisi fungsi-fungsi kecil yang dipakai lintas controller/view,
 * termasuk kontrak session dan proteksi role.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Escape output HTML.
 */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect lalu hentikan eksekusi.
 */
function redirect(string $target): void
{
    header('Location: ' . $target);
    exit;
}

/**
 * Simpan pesan flash sesuai kontrak session:
 * $_SESSION['flash'] = ['type' => 'success'|'danger', 'message' => '...'].
 */
function set_flash(string $type, string $message): void
{
    if (!in_array($type, ['success', 'danger'], true)) {
        $type = 'success';
    }

    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message,
    ];
}

/**
 * Ambil sekaligus hapus pesan flash.
 */
function take_flash(): ?array
{
    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        unset($_SESSION['flash']);
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    if (!isset($flash['type'], $flash['message'])) {
        return null;
    }

    if (!in_array($flash['type'], ['success', 'danger'], true)) {
        $flash['type'] = 'success';
    }

    return $flash;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function current_role(): ?string
{
    return $_SESSION['role'] ?? null;
}

/**
 * Halaman awal sesuai role.
 */
function home_for_role(?string $role = null): string
{
    $role = $role ?? current_role();

    switch ($role) {
        case 'admin':
            return 'index.php?page=admin-dashboard';
        case 'owner':
            return 'index.php?page=owner-dashboard';
        default:
            return 'index.php?page=user-katalog';
    }
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('danger', 'Silakan masuk terlebih dahulu.');
        redirect('index.php?page=login');
    }
}

/**
 * Proteksi role. Admin/owner yang salah masuk akan dilempar ke dashboard-nya.
 */
function require_role(string $role): void
{
    require_login();

    if (current_role() !== $role) {
        set_flash('danger', 'Anda tidak memiliki akses ke halaman tersebut.');
        redirect(home_for_role());
    }
}

/**
 * Halaman default saat ?page tidak dikirim.
 */
function default_page(): string
{
    if (is_logged_in()) {
        if (current_role() === 'admin') {
            return 'admin-dashboard';
        }
        if (current_role() === 'owner') {
            return 'owner-dashboard';
        }
    }

    return 'user-katalog';
}

function cart_items_count(): int
{
    $total = 0;

    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $qty) {
            $total += max(0, (int) $qty);
        }
    }

    return $total;
}

function format_rupiah($amount): string
{
    return 'Rp ' . number_format((float) $amount, 0, ',', '.');
}

function status_badge_class(string $status): string
{
    switch ($status) {
        case 'Menunggu Konfirmasi':
            return 'warning text-dark';
        case 'Diproses':
            return 'info text-dark';
        case 'Selesai':
            return 'success';
        case 'Dibatalkan':
            return 'danger';
        default:
            return 'secondary';
    }
}

/**
 * Hanya izinkan redirect internal (index.php?...).
 */
function safe_back_target(string $candidate, string $fallback = 'index.php?page=user-katalog'): string
{
    $candidate = trim($candidate);

    if ($candidate !== '' && strpos($candidate, 'index.php') === 0) {
        return $candidate;
    }

    return $fallback;
}

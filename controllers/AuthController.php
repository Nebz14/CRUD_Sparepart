<?php
/**
 * Controller autentikasi: login, register, logout.
 */
class AuthController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login(): void
    {
        if (is_logged_in()) {
            redirect(home_for_role());
        }

        $errors   = [];
        $username = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username === '' || $password === '') {
                $errors[] = 'Username dan password wajib diisi.';
            } else {
                $user = $this->userModel->findByUsername($username);

                if ($user && password_verify($password, $user['password'])) {
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = (int) $user['id_user'];
                    $_SESSION['nama']    = $user['nama'];
                    $_SESSION['role']    = $user['role'];

                    set_flash('success', 'Selamat datang, ' . $user['nama'] . '!');
                    redirect(home_for_role($user['role']));
                }

                $errors[] = 'Username atau password salah.';
            }
        }

        $pageTitle = 'Masuk - BengkelShop';
        require __DIR__ . '/../views/auth/login.php';
    }

    public function register(): void
    {
        if (is_logged_in()) {
            redirect(home_for_role());
        }

        $errors   = [];
        $nama     = '';
        $username = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama             = trim($_POST['nama'] ?? '');
            $username         = trim($_POST['username'] ?? '');
            $password         = $_POST['password'] ?? '';
            $passwordConfirm  = $_POST['password_confirm'] ?? '';

            if ($nama === '' || mb_strlen($nama) < 3) {
                $errors[] = 'Nama minimal 3 karakter.';
            }

            if (!preg_match('/^[A-Za-z0-9._]{3,30}$/', $username)) {
                $errors[] = 'Username 3-30 karakter, hanya huruf, angka, titik, dan garis bawah.';
            }

            if (strlen($password) < 6) {
                $errors[] = 'Password minimal 6 karakter.';
            }

            if ($password !== $passwordConfirm) {
                $errors[] = 'Konfirmasi password tidak cocok.';
            }

            if (empty($errors) && $this->userModel->usernameExists($username)) {
                $errors[] = 'Username sudah digunakan, silakan pilih yang lain.';
            }

            if (empty($errors)) {
                try {
                    // Pendaftaran mandiri selalu menghasilkan role "user".
                    $idUser = $this->userModel->create($nama, $username, $password, 'user');

                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $idUser;
                    $_SESSION['nama']    = $nama;
                    $_SESSION['role']    = 'user';

                    set_flash('success', 'Registrasi berhasil. Selamat datang, ' . $nama . '!');
                    redirect('index.php?page=user-katalog');
                } catch (PDOException $e) {
                    $errors[] = 'Registrasi gagal. Silakan coba lagi.';
                }
            }
        }

        $pageTitle = 'Daftar - BengkelShop';
        require __DIR__ . '/../views/auth/register.php';
    }

    public function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['nama'], $_SESSION['role'], $_SESSION['cart']);

        session_regenerate_id(true);

        set_flash('success', 'Anda telah keluar.');
        redirect('index.php?page=login');
    }
}

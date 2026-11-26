<?php
/**
 * Model user: autentikasi dan data akun.
 */
class UserModel
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_user, nama, username, password, role, created_at
             FROM tb_users
             WHERE username = :username
             LIMIT 1'
        );
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function findById(int $idUser): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_user, nama, username, role, created_at
             FROM tb_users
             WHERE id_user = :id_user
             LIMIT 1'
        );
        $stmt->execute([':id_user' => $idUser]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function usernameExists(string $username): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM tb_users WHERE username = :username');
        $stmt->execute([':username' => $username]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Buat akun baru. Password selalu di-hash dengan password_hash().
     */
    public function create(string $nama, string $username, string $password, string $role = 'user'): int
    {
        if (!in_array($role, ['admin', 'user', 'owner'], true)) {
            $role = 'user';
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO tb_users (nama, username, password, role)
             VALUES (:nama, :username, :password, :role)'
        );
        $stmt->execute([
            ':nama'     => $nama,
            ':username' => $username,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':role'     => $role,
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}

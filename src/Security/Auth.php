<?php

declare(strict_types=1);

namespace App\Security;

use App\Core\Session;
use PDO;

final readonly class Auth
{
    public function __construct(private PDO $pdo, private Session $session)
    {
    }

    public function attempt(string $email, string $password): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT u.id, u.name, u.email, u.password, u.active, r.slug AS role
             FROM users u JOIN roles r ON r.id = u.role_id WHERE u.email = :email LIMIT 1'
        );
        $statement->execute(['email' => strtolower(trim($email))]);
        $user = $statement->fetch();
        if (!is_array($user) || !(bool) $user['active'] || !password_verify($password, (string) $user['password'])) {
            return false;
        }

        unset($user['password'], $user['active']);
        $this->session->regenerate();
        $this->session->put('user', $user);
        $this->pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id')->execute(['id' => $user['id']]);
        $this->audit('auth.login');
        return true;
    }

    public function logout(): void
    {
        if ($this->check()) {
            $this->audit('auth.logout');
        }
        $this->session->forget('user');
        $this->session->regenerate();
    }

    public function check(): bool
    {
        return is_array($this->session->get('user'));
    }

    /** @return array<string, mixed>|null */
    public function user(): ?array
    {
        $user = $this->session->get('user');
        return is_array($user) ? $user : null;
    }

    public function can(string $permission): bool
    {
        $user = $this->user();
        if ($user === null) {
            return false;
        }
        $statement = $this->pdo->prepare(
            'SELECT COUNT(*) FROM role_permissions rp
             JOIN permissions p ON p.id = rp.permission_id
             JOIN users u ON u.role_id = rp.role_id
             WHERE u.id = :user_id AND p.name = :permission'
        );
        $statement->execute(['user_id' => $user['id'], 'permission' => $permission]);
        return (int) $statement->fetchColumn() > 0;
    }

    private function audit(string $action): void
    {
        $user = $this->user();
        $statement = $this->pdo->prepare(
            'INSERT INTO audit_logs (user_id, action, ip_address) VALUES (:user_id, :action, :ip)'
        );
        $statement->execute([
            'user_id' => $user['id'] ?? null,
            'action' => $action,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }
}

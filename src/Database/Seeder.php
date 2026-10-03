<?php

declare(strict_types=1);

namespace App\Database;

use App\Core\Database;
use PDO;

final readonly class Seeder
{
    public function __construct(private PDO $pdo)
    {
    }

    public function run(): void
    {
        Database::transaction($this->pdo, function (PDO $pdo): void {
            $pdo->exec("INSERT IGNORE INTO roles (name, slug) VALUES ('Administrador', 'admin'), ('Gestor', 'manager'), ('Analista', 'analyst')");
            $permissions = ['dashboard.view','departments.manage','employees.manage','projects.manage','transactions.manage','reports.view','users.manage'];
            $statement = $pdo->prepare('INSERT IGNORE INTO permissions (name) VALUES (?)');
            foreach ($permissions as $permission) {
                $statement->execute([$permission]);
            }
            $pdo->exec("INSERT IGNORE INTO role_permissions SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.slug = 'admin'");
            $password = password_hash('Admin@123', PASSWORD_DEFAULT);
            $user = $pdo->prepare("INSERT IGNORE INTO users (role_id, name, email, password) SELECT id, 'Administrador', 'admin@example.com', :password FROM roles WHERE slug = 'admin'");
            $user->execute(['password' => $password]);
        });
        echo "Database seeded.\n";
    }
}

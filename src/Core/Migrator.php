<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final readonly class Migrator
{
    public function __construct(private PDO $pdo, private string $path)
    {
    }

    public function migrate(): void
    {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS migrations (name VARCHAR(255) PRIMARY KEY, applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');
        $applied = $this->pdo->query('SELECT name FROM migrations')->fetchAll(PDO::FETCH_COLUMN);
        foreach (glob($this->path . '/*.sql') ?: [] as $file) {
            $name = basename($file);
            if (in_array($name, $applied, true)) {
                continue;
            }
            Database::transaction($this->pdo, function (PDO $pdo) use ($file, $name): void {
                $pdo->exec((string) file_get_contents($file));
                $statement = $pdo->prepare('INSERT INTO migrations (name) VALUES (:name)');
                $statement->execute(['name' => $name]);
            });
            echo "Migrated: {$name}\n";
        }
    }
}

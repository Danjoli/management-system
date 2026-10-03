<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
    /** @param array{dsn:string, username:string, password:string, options:array<int, mixed>} $config */
    public static function connect(array $config): PDO
    {
        return new PDO($config['dsn'], $config['username'], $config['password'], $config['options']);
    }

    public static function transaction(PDO $pdo, callable $operation): mixed
    {
        $pdo->beginTransaction();
        try {
            $result = $operation($pdo);
            $pdo->commit();
            return $result;
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
    }
}

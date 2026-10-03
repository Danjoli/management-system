<?php

declare(strict_types=1);

return [
    'name' => 'Sistema de Gestão',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL),
    'url' => rtrim($_ENV['APP_URL'] ?? 'http://localhost:8080', '/'),
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo',
    'session' => $_ENV['SESSION_NAME'] ?? 'management_session',
];

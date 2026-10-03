<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Container;
use App\Core\Database;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (is_file(dirname(__DIR__) . '/.env')) {
    Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
}

$appConfig = require dirname(__DIR__) . '/config/app.php';
$databaseConfig = require dirname(__DIR__) . '/config/database.php';
date_default_timezone_set($appConfig['timezone']);

$container = new Container();
$container->set('config', $appConfig);
$container->set(PDO::class, static fn (): PDO => Database::connect($databaseConfig));

$app = new Application($container);
(require dirname(__DIR__) . '/routes/web.php')($app);

return $app;


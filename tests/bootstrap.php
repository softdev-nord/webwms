<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../config/bootstrap.php')) {
    require __DIR__ . '/../config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    $_ENV['APP_ENV'] = 'test';
    new Dotenv()->bootEnv(__DIR__ . '/../.env.test');
}

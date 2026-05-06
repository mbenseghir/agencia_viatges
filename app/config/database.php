<?php
return [
    'driver' => $_ENV['DB_DRIVER'] ?? 'sqlite',
    'database' => isset($_ENV['DB_DATABASE']) ? ROOT_PATH . '/' . $_ENV['DB_DATABASE'] : __DIR__ . '/../../database/database.sqlite',
    'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'dbname' => $_ENV['DB_DATABASE'] ?? 'agencia_viatges',
    'user' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => 'utf8mb4',
];

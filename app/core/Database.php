<?php
declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $config = \config('database');
        if (!is_array($config)) {
            throw new RuntimeException('La configuració de base de dades no és vàlida.');
        }

        $host = (string)($config['host'] ?? '127.0.0.1');
        $port = (string)($config['port'] ?? '3306');
        $dbname = (string)($config['dbname'] ?? 'agencia_viatges');
        $charset = (string)($config['charset'] ?? 'utf8mb4');
        $user = (string)($config['user'] ?? 'root');
        $password = (string)($config['password'] ?? '');

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

        try {
            self::$pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            $safeMessage = 'No s’ha pogut connectar amb la base de dades. Revisa app/config/database.php.';
            if (\config('app', 'environment') === 'development') {
                $safeMessage .= ' Detall tècnic: ' . $e->getMessage();
            }
            throw new RuntimeException($safeMessage, 0, $e);
        }

        return self::$pdo;
    }

    public static function transaction(callable $callback): mixed
    {
        $pdo = self::connection();
        $pdo->beginTransaction();

        try {
            $result = $callback($pdo);
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}

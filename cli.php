<?php
declare(strict_types=1);

require_once __DIR__ . '/app/core/bootstrap.php';

$command = $argv[1] ?? 'help';

switch ($command) {
    case 'serve':
        $host = $argv[2] ?? 'localhost';
        $port = $argv[3] ?? '8000';
        echo "Iniciant el servidor de desenvolupament a http://{$host}:{$port}\n";
        echo "Prem Ctrl+C per aturar-lo.\n";
        
        $docroot = __DIR__ . '/public';
        $serverFile = __DIR__ . '/server.php';
        
        // Utilitzem escapeshellarg per seguretat
        $cmd = sprintf('php -S %s:%s -t %s %s', escapeshellarg($host), escapeshellarg($port), escapeshellarg($docroot), escapeshellarg($serverFile));
        passthru($cmd);
        break;

    case 'migrate':
        echo "Executant les migracions (base de dades)...\n";
        try {
            $config = require __DIR__ . '/app/config/database.php';
            $driver = $config['driver'] ?? 'mysql';
            
            if ($driver === 'sqlite') {
                $dbPath = $config['database'] ?? __DIR__ . '/database/database.sqlite';
                if (!file_exists($dbPath)) {
                    touch($dbPath);
                }
                $pdo = new PDO("sqlite:{$dbPath}");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $sqlFile = __DIR__ . '/database/agencia_viatges_sqlite.sql';
            } else {
                $host = $config['host'] ?? '127.0.0.1';
                $port = $config['port'] ?? '3306';
                $user = $config['user'] ?? 'root';
                $password = $config['password'] ?? '';
                $charset = $config['charset'] ?? 'utf8mb4';

                // 1. Connectem sense dbname per poder crear la base de dades
                $dsn = "mysql:host={$host};port={$port};charset={$charset}";
                $pdo = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);

                $sqlFile = __DIR__ . '/database/agencia_viatges.sql';
            }
            
            if (!file_exists($sqlFile)) {
                echo "Error: No s'ha trobat el fitxer SQL a {$sqlFile}\n";
                exit(1);
            }
            
            $sql = file_get_contents($sqlFile);
            $pdo->exec($sql);
            
            echo "Base de dades configurada i migrada correctament!\n";
        } catch (\Throwable $e) {
            echo "Error durant la migració: " . $e->getMessage() . "\n";
            exit(1);
        }
        break;

    case 'help':
    default:
        echo "Comandes disponibles:\n";
        echo "  php cli.php serve [host] [port]   Inicia el servidor PHP (per defecte localhost:8000)\n";
        echo "  php cli.php migrate               Crea la base de dades utilitzant el fitxer SQL\n";
        echo "  php cli.php help                  Mostra aquesta ajuda\n";
        break;
}

<?php
declare(strict_types=1);

session_start();

define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/views');
define('STORAGE_PATH', ROOT_PATH . '/storage');

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Core\\' => APP_PATH . '/core/',
        'Controllers\\' => APP_PATH . '/controllers/',
        'Models\\' => APP_PATH . '/models/',
        'Services\\' => APP_PATH . '/services/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($file)) {
            require $file;
        }
    }
});

require_once APP_PATH . '/core/helpers.php';

<?php

/**
 * Laravel-style server.php
 * This file allows us to run the application using PHP's built-in web server
 * from the project root using: php -S localhost:8000 server.php
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Serve static files from the public directory
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

// Redirect everything else to public/index.php
require_once __DIR__.'/public/index.php';

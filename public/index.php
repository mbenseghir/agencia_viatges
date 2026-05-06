<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/app/core/bootstrap.php';

// Carregar contenidor de dependències
$container = require_once ROOT_PATH . '/app/core/container.php';

// Inicialitzar enrutador
$router = new \Bramus\Router\Router();

// Carregar les rutes definides a routes/web.php
require_once ROOT_PATH . '/routes/web.php';

// Despatxar la ruta actual
$router->run();

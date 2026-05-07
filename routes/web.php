<?php
declare(strict_types=1);

use Bramus\Router\Router;
use Controllers\AdminController;
use Controllers\AuthController;
use Controllers\HomeController;
use Controllers\PromocioController;
use Controllers\ReservaController;

/** @var Router $router */
/** @var DI\Container $container */

// Middlewares
$router->before('POST', '/.*', function() use ($container) {
    $container->call([\Middlewares\CsrfMiddleware::class, 'handle']);
});

$router->before('GET|POST', '/admin.*', function() use ($container) {
    $container->call([\Middlewares\AuthMiddleware::class, 'handle']);
});

// Rutes Públiques
$router->get('/', function() use ($container) { $container->call([HomeController::class, 'index']); });
$router->get('/home', function() use ($container) { $container->call([HomeController::class, 'index']); });

$router->get('/promocions', function() use ($container) { $container->call([PromocioController::class, 'index']); });
$router->get('/promocio/(\d+)', function($id) use ($container) { $container->call([PromocioController::class, 'show'], ['id' => (int)$id]); });

$router->get('/reserva/crear/(\d+)', function($id) use ($container) { $container->call([ReservaController::class, 'create'], ['promocioId' => (int)$id]); });
$router->post('/reserva/crear/(\d+)', function($id) use ($container) { $container->call([ReservaController::class, 'store'], ['promocioId' => (int)$id]); });
$router->get('/reserva/gracies/(\d+)', function($id) use ($container) { $container->call([ReservaController::class, 'success'], ['id' => (int)$id]); });

// Autenticació
$router->get('/auth/login', function() use ($container) { $container->call([AuthController::class, 'showLogin']); });
$router->post('/auth/login', function() use ($container) { $container->call([AuthController::class, 'login']); });
$router->get('/auth/logout', function() use ($container) { $container->call([AuthController::class, 'logout']); });

// Rutes Admin
$router->mount('/admin', function() use ($router, $container) {
    $router->get('/', function() use ($container) { $container->call([AdminController::class, 'dashboard']); });
    $router->get('/dashboard', function() use ($container) { $container->call([AdminController::class, 'dashboard']); });
    $router->get('/reservas', function() use ($container) { $container->call([AdminController::class, 'reservas']); });
    
    $router->get('/reserva/(\d+)', function($id) use ($container) { $container->call([AdminController::class, 'reserva'], ['id' => (int)$id]); });
    
    $router->post('/reserva/acceptar', function() use ($container) { $container->call([AdminController::class, 'acceptar'], ['id' => (int)($_POST['id_reserva'] ?? 0)]); });
    $router->post('/reserva/rebutjar', function() use ($container) { $container->call([AdminController::class, 'rebutjar'], ['id' => (int)($_POST['id_reserva'] ?? 0)]); });
    $router->post('/reserva/formalitzar', function() use ($container) { $container->call([AdminController::class, 'formalitzar'], ['id' => (int)($_POST['id_reserva'] ?? 0)]); });
});

$router->set404(function() {
    http_response_code(404);
    \Core\View::render('error', [
        'title' => 'Pàgina no trobada',
        'message' => 'La pàgina sol·licitada no existeix.',
    ]);
});

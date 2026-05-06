<?php
declare(strict_types=1);

namespace Core;

use Controllers\AdminController;
use Controllers\AuthController;
use Controllers\HomeController;
use Controllers\PromocioController;
use Controllers\ReservaController;
use Throwable;

final class Router
{
    public function dispatch(): void
    {
        $route = trim((string)($_GET['r'] ?? 'home'), '/');
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        try {
            match ($route) {
                '', 'home' => (new HomeController())->index(),
                'promocions' => (new PromocioController())->index(),
                'promocio' => (new PromocioController())->show((int)($_GET['id'] ?? 0)),
                'reserva/crear' => $method === 'POST'
                    ? (new ReservaController())->store((int)($_GET['promocio_id'] ?? 0))
                    : (new ReservaController())->create((int)($_GET['promocio_id'] ?? 0)),
                'reserva/gracies' => (new ReservaController())->success((int)($_GET['id'] ?? 0)),

                'auth/login' => $method === 'POST'
                    ? (new AuthController())->login()
                    : (new AuthController())->showLogin(),
                'auth/logout' => (new AuthController())->logout(),

                'admin', 'admin/dashboard' => (new AdminController())->dashboard(),
                'admin/reservas' => (new AdminController())->reservas(),
                'admin/reserva' => (new AdminController())->reserva((int)($_GET['id'] ?? 0)),
                'admin/reserva/acceptar' => (new AdminController())->acceptar((int)($_POST['id_reserva'] ?? 0)),
                'admin/reserva/rebutjar' => (new AdminController())->rebutjar((int)($_POST['id_reserva'] ?? 0)),
                'admin/reserva/formalitzar' => (new AdminController())->formalitzar((int)($_POST['id_reserva'] ?? 0)),
                default => $this->notFound(),
            };
        } catch (Throwable $e) {
            http_response_code(500);
            View::render('error', [
                'title' => 'Error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function notFound(): void
    {
        http_response_code(404);
        View::render('error', [
            'title' => 'Pàgina no trobada',
            'message' => 'La pàgina sol·licitada no existeix.',
        ]);
    }
}

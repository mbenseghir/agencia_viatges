<?php
declare(strict_types=1);

namespace Controllers;

use Core\View;
use Models\Promocio;

final class PromocioController
{
    public function __construct(private Promocio $promocioModel) {}

    public function index(): void
    {
        $promocions = $this->promocioModel->active();
        View::render('public/promocions', [
            'title' => 'Promocions disponibles',
            'promocions' => $promocions,
        ]);
    }

    public function show(int $id): void
    {
        $promocio = $this->promocioModel->find($id);
        if (!$promocio) {
            http_response_code(404);
            View::render('error', [
                'title' => 'Promoció no trobada',
                'message' => 'No hem trobat aquesta promoció.',
            ]);
            return;
        }

        View::render('public/promocio_show', [
            'title' => $promocio['paquet_nom'],
            'promocio' => $promocio,
        ]);
    }
}

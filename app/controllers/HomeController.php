<?php
declare(strict_types=1);

namespace Controllers;

use Core\View;
use Models\Promocio;

final class HomeController
{
    public function __construct(private Promocio $promocioModel) {}

    public function index(): void
    {
        $promocions = $this->promocioModel->active();
        View::render('public/home', [
            'title' => 'Paquets en promoció',
            'promocions' => $promocions,
        ]);
    }
}

<?php
declare(strict_types=1);

namespace Controllers;

use Core\View;
use Models\Promocio;

final class HomeController
{
    public function index(): void
    {
        $promocions = (new Promocio())->active();
        View::render('public/home', [
            'title' => 'Paquets en promoció',
            'promocions' => $promocions,
        ]);
    }
}

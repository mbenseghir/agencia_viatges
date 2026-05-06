<?php
declare(strict_types=1);

namespace Core;

final class View
{
    public static function render(string $view, array $data = [], string $layout = 'layout'): void
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require VIEW_PATH . '/' . $view . '.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/' . $layout . '.php';
    }
}

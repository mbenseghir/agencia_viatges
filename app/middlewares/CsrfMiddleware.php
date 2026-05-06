<?php
declare(strict_types=1);

namespace Middlewares;

final class CsrfMiddleware
{
    public function handle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            \verify_csrf();
        }
    }
}

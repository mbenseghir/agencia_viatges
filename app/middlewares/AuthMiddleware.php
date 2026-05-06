<?php
declare(strict_types=1);

namespace Middlewares;

final class AuthMiddleware
{
    public function handle(): void
    {
        if (!\is_admin()) {
            \flash('warning', 'Has d’iniciar sessió per accedir al panell intern.');
            \redirect('auth/login');
        }
    }
}

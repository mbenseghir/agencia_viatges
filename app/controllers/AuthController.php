<?php
declare(strict_types=1);

namespace Controllers;

use Core\View;
use Models\Usuari;

final class AuthController
{
    public function showLogin(): void
    {
        View::render('auth/login', [
            'title' => 'Accés administració',
        ]);
    }

    public function login(): void
    {
        \verify_csrf();

        $email = \post_string('email', 190);
        $password = (string)($_POST['password'] ?? '');
        $user = (new Usuari())->findByEmail($email);

        if (!$user || !password_verify($password, (string)$user['password_hash'])) {
            \flash('danger', 'Credencials incorrectes.');
            \redirect('auth/login');
        }

        $key = \config('app', 'admin_session_key', 'agencia_admin_user');
        $_SESSION[$key] = [
            'id' => (int)$user['id_usuari'],
            'nom' => $user['nom'],
            'email' => $user['email'],
            'rol' => $user['rol'],
        ];

        \flash('success', 'Sessió iniciada correctament.');
        \redirect('admin/dashboard');
    }

    public function logout(): void
    {
        $key = \config('app', 'admin_session_key', 'agencia_admin_user');
        unset($_SESSION[$key]);
        \flash('success', 'Sessió tancada.');
        \redirect('home');
    }
}

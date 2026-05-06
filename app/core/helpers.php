<?php
declare(strict_types=1);

function config(string $file, ?string $key = null, mixed $default = null): mixed
{
    static $configs = [];

    if (!isset($configs[$file])) {
        $path = APP_PATH . '/config/' . $file . '.php';
        if (!is_file($path)) {
            return $default;
        }
        $loaded = require $path;
        $configs[$file] = is_array($loaded) ? $loaded : [];
    }

    if ($key === null) {
        return $configs[$file];
    }

    return $configs[$file][$key] ?? $default;
}

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $route = 'home', array $params = []): string
{
    $params = array_merge(['r' => $route], $params);
    return 'index.php?' . http_build_query($params);
}

function redirect(string $route, array $params = []): never
{
    header('Location: ' . url($route, $params));
    exit;
}

function money(mixed $value): string
{
    return number_format((float)$value, 2, ',', '.') . ' €';
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['_csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['_csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Token CSRF no vàlid. Torna enrere i prova-ho de nou.');
    }
}

function flash(?string $type = null, ?string $message = null): mixed
{
    if ($type !== null && $message !== null) {
        $_SESSION['_flash'][$type][] = $message;
        return null;
    }

    $messages = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $messages;
}

function current_admin(): ?array
{
    $key = config('app', 'admin_session_key', 'agencia_admin_user');
    return $_SESSION[$key] ?? null;
}

function is_admin(): bool
{
    return current_admin() !== null;
}

function require_admin(): void
{
    if (!is_admin()) {
        flash('warning', 'Has d’iniciar sessió per accedir al panell intern.');
        redirect('auth/login');
    }
}

function post_string(string $key, int $max = 255): string
{
    $value = trim((string)($_POST[$key] ?? ''));
    if (mb_strlen($value) > $max) {
        $value = mb_substr($value, 0, $max);
    }
    return $value;
}

function post_bool(string $key): bool
{
    return isset($_POST[$key]) && in_array((string)$_POST[$key], ['1', 'S', 'true', 'on'], true);
}

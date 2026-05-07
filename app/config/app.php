<?php
return [
    'name' => $_ENV['APP_NAME'] ?? 'Agència ProTravel',
    'base_url' => $_ENV['APP_URL'] ?? '',
    'environment' => $_ENV['APP_ENV'] ?? 'development',
    'provider_mail_mode' => $_ENV['MAIL_MODE'] ?? 'log', // log | mail
    'admin_session_key' => $_ENV['ADMIN_SESSION_KEY'] ?? 'agencia_admin_user',
];

<?php
declare(strict_types=1);

namespace Core;

final class View
{
    private static ?\Twig\Environment $twig = null;

    private static function getTwig(): \Twig\Environment
    {
        if (self::$twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(VIEW_PATH);
            self::$twig = new \Twig\Environment($loader, [
                'cache' => false, // STORAGE_PATH . '/cache/twig' en producció
                'debug' => true,
            ]);

            // Afegir funcions globals (helpers)
            self::$twig->addFunction(new \Twig\TwigFunction('url', 'url'));
            self::$twig->addFunction(new \Twig\TwigFunction('e', 'e'));
            self::$twig->addFunction(new \Twig\TwigFunction('money', 'money'));
            self::$twig->addFunction(new \Twig\TwigFunction('csrf_field', 'csrf_field', ['is_safe' => ['html']]));
            self::$twig->addFunction(new \Twig\TwigFunction('flash', 'flash'));
            self::$twig->addFunction(new \Twig\TwigFunction('is_admin', 'is_admin'));
            self::$twig->addFunction(new \Twig\TwigFunction('config', 'config'));
        }
        return self::$twig;
    }

    public static function render(string $view, array $data = []): void
    {
        echo self::getTwig()->render($view . '.twig', $data);
    }
}

<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    /** @param array<string, mixed> $data */
    public static function render(string $template, array $data = [], string $layout = 'layouts/app'): Response
    {
        $base = dirname(__DIR__) . '/Views/';
        $file = $base . $template . '.php';
        if (!is_file($file)) {
            throw new RuntimeException("View {$template} not found.");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        $content = (string) ob_get_clean();
        if ($layout === '') {
            return new Response($content);
        }
        ob_start();
        require $base . $layout . '.php';
        return new Response((string) ob_get_clean());
    }
}

<?php

namespace App\Core;

class View
{
    private static string $templatesPath = __DIR__ . '/../../templates/';

    public static function render(string $template, array $data = []): void
    {
        extract($data);
        
        $templateFile = self::$templatesPath . $template . '.php';
        
        if (!file_exists($templateFile)) {
            throw new \RuntimeException("Template nicht gefunden: " . $template);
        }

        require $templateFile;
    }

    public static function renderWithLayout(string $template, array $data = [], string $layout = 'layout/main'): void
    {
        $data['content'] = self::capture($template, $data);
        self::render($layout, $data);
    }

    private static function capture(string $template, array $data = []): string
    {
        extract($data);
        
        $templateFile = self::$templatesPath . $template . '.php';
        
        if (!file_exists($templateFile)) {
            throw new \RuntimeException("Template nicht gefunden: " . $template);
        }

        ob_start();
        require $templateFile;
        return ob_get_clean();
    }
}

<?php

namespace App\Core;

class View
{
    private static string $templatesPath = __DIR__ . '/../../templates/';

    public static function render(string $template, array $data = []): void
    {
        extract($data);
        require self::$templatesPath . $template . '.php';
    }
}

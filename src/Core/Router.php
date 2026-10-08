<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): self
    {
        $this->routes['GET'][$path] = $handler;
        return $this;
    }

    public function post(string $path, callable|array $handler): self
    {
        $this->routes['POST'][$path] = $handler;
        return $this;
    }

    public function resolve(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove trailing slash except for root
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        // Zustandsändernde Anfragen nur mit gültigem CSRF-Token aus dem eigenen Formular
        if ($method === 'POST' && !Csrf::isValid(Request::post(Csrf::FIELD))) {
            http_response_code(403);
            View::render('pages/csrf-error', ['back' => $this->refererPath()]);
            return;
        }

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo "Seite nicht gefunden";
            return;
        }

        if (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            $controller->$method();
        } else {
            $handler();
        }
    }

    /** Pfad der vorherigen Seite, falls sie zu dieser Website gehört; sonst die Startseite. */
    private function refererPath(): string
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $host = preg_quote($_SERVER['HTTP_HOST'] ?? '', '#');

        // \S: Browser entfernen Tabs/Zeilenumbrüche aus URLs ("/\t/evil.example" → "//evil.example")
        if (!preg_match('#^https?://' . $host . '(/\S*)$#', $referer, $m)) {
            return '/';
        }

        // "//evil.example" und "/\evil.example" (Browser lesen "\" wie "/") wären
        // protokollrelative Links auf eine fremde Domain
        return '/' . ltrim($m[1], '/\\');
    }
}

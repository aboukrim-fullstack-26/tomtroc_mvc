<?php
namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $params = []): void
    {
        View::render($view, $params);
    }

    protected function requirePostCsrf(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Méthode non autorisée.');
        }
        $token = $_POST['csrf_token'] ?? null;
        if (!Csrf::check($token)) {
            http_response_code(403);
            exit('Token CSRF invalide.');
        }
    }
}

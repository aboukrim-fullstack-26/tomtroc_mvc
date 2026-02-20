<?php

/*
 * TomTroc — Router (Front Controller)
 * - Déclare les routes GET/POST
 * - Normalise l’URL (Uniform Server : /tomtroc_mvc/public)
 * - Résout Route -> Controller::method
 * Flux : URL -> Router -> Controller -> Model(s) -> View
 */


namespace App\Core;

use App\Core\ModuleManager;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, array $action): void
    {
        $this->routes['GET'][$this->normalize($path)] = $action;
    }

    public function post(string $path, array $action): void
    {
        $this->routes['POST'][$this->normalize($path)] = $action;
    }

    public function run(): void
    {
        $this->defineRoutes();

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Uniform Server en sous-dossier
        $base = '/tomtroc_mvc/public';
        if (str_starts_with($path, $base)) {
            $path = substr($path, strlen($base)) ?: '/';
        }

        $path = $this->normalize($path);

        $action = $this->routes[$method][$path] ?? null;

        if (!$action) {
            // Si une route n'existe pas, on tente d'expliquer si c'est un module désactivé.
            $modules = new \App\Core\ModuleManager();

            $disabledMap = [
                '/favoris' => 'Favorites',
                '/favori' => 'Favorites',
                '/favori/ajouter' => 'Favorites',
                '/favori/supprimer' => 'Favorites',
                '/mes-signalements' => 'Reports',
                '/signalement/livre' => 'Reports',
                '/messagerie' => 'Message',
                '/messages' => 'Message',
                '/message/nouveau' => 'Message',
                '/livres' => 'Book',
                '/livre' => 'Book',
                '/livre/creer' => 'Book',
                '/livre/editer' => 'Book',
                '/livre/supprimer' => 'Book',
                '/connexion' => 'Auth',
                '/inscription' => 'Auth',
                '/deconnexion' => 'Auth',
            ];

            $modName = $disabledMap[$path] ?? null;
            if ($modName && !$modules->isEnabled($modName)) {
                http_response_code(503);
                echo "Module désactivé : " . htmlspecialchars($modName);
                return;
            }

            http_response_code(404);
            echo "404 - Page introuvable";
            return;
        }

        [$controllerClass, $methodName] = $action;

        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo "500 - Contrôleur introuvable : " . htmlspecialchars($controllerClass);
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $methodName)) {
            http_response_code(500);
            echo "500 - Méthode introuvable : " . htmlspecialchars($controllerClass . '::' . $methodName);
            return;
        }

        $controller->$methodName();
    }

    public function dispatch(): void
    {
        $this->run();
    }

    private function defineRoutes(): void
    {
        // Core
        $this->get('/', [\App\Controllers\HomeController::class, 'index']);

        // Pages statiques (footer)
        $this->get('/politique-confidentialite', [\App\Controllers\PagesController::class, 'privacy']);
        $this->get('/mentions-legales', [\App\Controllers\PagesController::class, 'legal']);

        // Modules (activables/désactivables via config/modules.php)
        $modules = new \App\Core\ModuleManager();
        $modules->registerRoutes($this);
    }

    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }
}
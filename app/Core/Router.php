<?php
/**
 * Router (résolution URL → Controller::méthode)
 *
 * Rôle :
 * - Point d’entrée / composant du MVC TomTroc.
 * - Commentaires ajoutés pour faciliter debug & évolutions (V4 stable).
 *
 * Ordre d’exécution (général) :
 * public/index.php → app/bootstrap.php → Router → Controller → Model(s) → View(s)
 *
 * @author aboukrim
 * @date 2026-02-10
 */

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

    /**
     * Méthode : get()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public function get(string $path, array $action): void
    {
        $this->routes['GET'][$this->normalize($path)] = $action;
    }

    /**
     * Méthode : post()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public function post(string $path, array $action): void
    {
        $this->routes['POST'][$this->normalize($path)] = $action;
    }

    /**
     * Méthode : run()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
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

    /**
     * Méthode : dispatch()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public function dispatch(): void
    {
        $this->run();
    }

    /**
     * Méthode : defineRoutes()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    private function defineRoutes(): void
    {
        // Home
        $this->get('/', [\App\Controllers\HomeController::class, 'index']);

        $modules = new ModuleManager();
        $modules->registerRoutes($this);

        // Livres
        $this->get('/livres', [\App\Controllers\BookController::class, 'index']);
        $this->get('/livre', [\App\Controllers\BookController::class, 'show']); // ?id=

        // Gestion des livres (Mon compte)
        $this->get('/livre/creer', [\App\Controllers\BookController::class, 'create']);
        $this->post('/livre/creer', [\App\Controllers\BookController::class, 'create']);
        $this->get('/livre/editer', [\App\Controllers\BookController::class, 'edit']); // ?id=
        $this->post('/livre/editer', [\App\Controllers\BookController::class, 'edit']); // ?id=
        $this->post('/livre/supprimer', [\App\Controllers\BookController::class, 'delete']);


        // Auth
        /*$this->get('/connexion', [\App\Controllers\AuthController::class, 'loginForm']);
        $this->post('/connexion', [\App\Controllers\AuthController::class, 'login']);
        $this->get('/inscription', [\App\Controllers\AuthController::class, 'registerForm']);
        $this->post('/inscription', [\App\Controllers\AuthController::class, 'register']);
        $this->get('/deconnexion', [\App\Controllers\AuthController::class, 'logout']);*/
		
		// Auth (ton controller gère GET + POST dans les mêmes méthodes)
		$this->get('/connexion', [\App\Controllers\AuthController::class, 'login']);
		$this->post('/connexion', [\App\Controllers\AuthController::class, 'login']);

		$this->get('/inscription', [\App\Controllers\AuthController::class, 'register']);
		$this->post('/inscription', [\App\Controllers\AuthController::class, 'register']);

		$this->get('/deconnexion', [\App\Controllers\AuthController::class, 'logout']);

        // Compte
        $this->get('/mon-compte', [\App\Controllers\AccountController::class, 'index']);
        $this->post('/mon-compte', [\App\Controllers\AccountController::class, 'index']); // POST: enregistrer profil
        $this->get('/profil', [\App\Controllers\AccountController::class, 'publicProfile']); // ?id=

        // Messagerie (✅ maquette + alias)
        $this->get('/messagerie', [\App\Controllers\MessageController::class, 'index']);
        $this->get('/messages', [\App\Controllers\MessageController::class, 'index']);
        $this->post('/message/nouveau', [\App\Controllers\MessageController::class, 'startOrSend']);
    }

    /**
     * Méthode : normalize()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }
}
<?php
/**
 * Controller de base (helpers de rendu, sécurité)
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
namespace App\Core;

abstract class Controller
{
    /**
     * Méthode : render()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    protected function render(string $view, array $params = []): void
    {
        View::render($view, $params);
    }

    /**
     * Méthode : requirePostCsrf()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
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

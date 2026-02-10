<?php
/**
 * View renderer (layouts + variables)
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

final class View
{
    /**
     * Méthode : render()
     * Rôle : logique du composant (Controller/Model/Core).
     * Exécution : appelée par le Router ou par une autre couche (selon le fichier).
     */
    public static function render(string $view, array $params = []): void
    {
        extract($params, EXTR_SKIP);

        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            exit('Vue introuvable: ' . Helpers::e($view));
        }

        require __DIR__ . '/../Views/layouts/header.php';
        require $viewFile;
        require __DIR__ . '/../Views/layouts/footer.php';
    }
}

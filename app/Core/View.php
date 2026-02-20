<?php
namespace App\Core;

final class View
{
    public static function render(string $view, array $params = []): void
    {
        extract($params, EXTR_SKIP);

        // ✅ Rend le ModuleManager disponible dans TOUTES les vues (header/footer inclus)
        // Permet: if ($modules->isEnabled('...')) dans les layouts.
        $modules = new ModuleManager();

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
